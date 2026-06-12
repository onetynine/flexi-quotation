const { app, BrowserWindow, shell, dialog, ipcMain } = require('electron');
const { autoUpdater } = require('electron-updater');
const { spawn, execFileSync } = require('child_process');
const path = require('path');
const net = require('net');
const fs = require('fs');

const PORT = 8765;
let mainWindow = null;
let phpProcess = null;
let currentDbPath = null;
let phpBin = null;

// ── Config ────────────────────────────────────────────────────────────────────

function getConfigPath() {
    return path.join(app.getPath('userData'), 'config.json');
}

function loadConfig() {
    try { return JSON.parse(fs.readFileSync(getConfigPath(), 'utf8')); }
    catch (_) { return {}; }
}

function saveConfig(data) {
    const cfg = loadConfig();
    fs.writeFileSync(getConfigPath(), JSON.stringify({ ...cfg, ...data }, null, 2));
}

function getMachineCode() {
    const cfg = loadConfig();
    return cfg.machineCode || 'MV';
}

// ── PHP / paths ───────────────────────────────────────────────────────────────

function findPhp() {
    if (app.isPackaged) {
        return path.join(process.resourcesPath, 'php', 'php.exe');
    }
    const candidates = [
        'C:\\Users\\Smart Rental\\.config\\herd\\bin\\php84\\php.exe',
        'php',
        'C:\\php\\php.exe',
        'C:\\xampp\\php\\php.exe',
    ];
    for (const p of candidates) {
        try { execFileSync(p, ['-r', 'echo 1;'], { windowsHide: true, timeout: 3000 }); return p; }
        catch (_) {}
    }
    return 'php';
}

function getLaravelPath() {
    return app.isPackaged
        ? path.join(process.resourcesPath, 'laravel')
        : path.join(__dirname, '..', 'laravel');
}

function getDbPath() {
    const cfg = loadConfig();
    return cfg.dbPath || path.join(app.getPath('userData'), 'database.sqlite');
}

function ensureDb(dbPath) {
    const dir = path.dirname(dbPath);
    if (!fs.existsSync(dir)) fs.mkdirSync(dir, { recursive: true });
    if (!fs.existsSync(dbPath)) fs.writeFileSync(dbPath, '');
}

// ── Laravel ───────────────────────────────────────────────────────────────────

function getStoragePath() {
    return path.join(app.getPath('userData'), 'storage');
}

function ensureStorage(storagePath) {
    for (const dir of ['app', 'framework/cache/data', 'framework/sessions', 'framework/views', 'logs', 'fonts']) {
        fs.mkdirSync(path.join(storagePath, dir), { recursive: true });
    }
}

function phpEnv(dbPath, machineCode, storagePath) {
    return {
        ...process.env,
        DB_DATABASE: dbPath,
        APP_MACHINE_CODE: machineCode,
        APP_STORAGE_PATH: storagePath,
    };
}

function runMigrations(php, laravelPath, dbPath, machineCode, storagePath) {
    execFileSync(php, ['artisan', 'migrate', '--force'], {
        cwd: laravelPath,
        env: phpEnv(dbPath, machineCode, storagePath),
        windowsHide: true,
        timeout: 60000,
    });
}

function startPhpServer(php, laravelPath, dbPath, machineCode, storagePath) {
    phpProcess = spawn(php, ['artisan', 'serve', `--port=${PORT}`, '--host=127.0.0.1'], {
        cwd: laravelPath,
        env: phpEnv(dbPath, machineCode, storagePath),
        windowsHide: true,
    });
    phpProcess.stderr.on('data', d => console.log('[PHP]', d.toString()));
    phpProcess.on('exit', code => console.log('[PHP] exit', code));
}

function waitForPort(port, timeout = 20000) {
    return new Promise((resolve, reject) => {
        const start = Date.now();
        const check = () => {
            const sock = new net.Socket();
            sock.setTimeout(500);
            sock.on('connect', () => { sock.destroy(); resolve(); });
            sock.on('error', () => {
                sock.destroy();
                if (Date.now() - start > timeout) reject(new Error('Server did not start'));
                else setTimeout(check, 300);
            });
            sock.connect(port, '127.0.0.1');
        };
        check();
    });
}

// ── Window ────────────────────────────────────────────────────────────────────

function createWindow() {
    mainWindow = new BrowserWindow({
        width: 1280, height: 800, minWidth: 960, minHeight: 600,
        title: 'Flexi Quotation - Smart Rental',
        webPreferences: {
            nodeIntegration: false,
            contextIsolation: true,
            preload: path.join(__dirname, 'preload.js'),
        },
    });
    mainWindow.loadURL(`http://127.0.0.1:${PORT}`);
    mainWindow.webContents.setWindowOpenHandler(({ url }) => {
        if (!url.startsWith(`http://127.0.0.1:${PORT}`)) {
            shell.openExternal(url); return { action: 'deny' };
        }
        return { action: 'allow' };
    });
    mainWindow.on('closed', () => { mainWindow = null; });
}

// ── IPC ───────────────────────────────────────────────────────────────────────

function setupIpc() {
    ipcMain.handle('get-db-info', () => ({
        path: currentDbPath,
        version: app.getVersion(),
        machineCode: loadConfig().machineCode || '',
    }));

    ipcMain.handle('set-machine-code', (_, code) => {
        const clean = code.trim().toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 2);
        if (clean.length !== 2) return { error: 'Must be exactly 2 letters/numbers' };
        saveConfig({ machineCode: clean });
        return { code: clean };
    });

    ipcMain.handle('change-db-path', async () => {
        const result = await dialog.showOpenDialog(mainWindow, {
            title: 'Select Database Folder',
            properties: ['openDirectory'],
        });
        if (result.canceled) return { canceled: true };
        const newPath = path.join(result.filePaths[0], 'database.sqlite');
        saveConfig({ dbPath: newPath });
        return { path: newPath };
    });

    ipcMain.handle('backup-db', async () => {
        const ts = new Date().toISOString().slice(0, 10);
        const result = await dialog.showSaveDialog(mainWindow, {
            title: 'Backup Database',
            defaultPath: `flexi-quotation-backup-${ts}.sqlite`,
            filters: [{ name: 'SQLite Database', extensions: ['sqlite'] }],
        });
        if (result.canceled) return { canceled: true };
        fs.copyFileSync(currentDbPath, result.filePath);
        return { path: result.filePath };
    });

    ipcMain.handle('restore-db', async () => {
        const result = await dialog.showOpenDialog(mainWindow, {
            title: 'Restore Database',
            properties: ['openFile'],
            filters: [{ name: 'SQLite Database', extensions: ['sqlite'] }],
        });
        if (result.canceled) return { canceled: true };
        if (phpProcess) { phpProcess.kill(); phpProcess = null; }
        fs.copyFileSync(result.filePaths[0], currentDbPath);
        app.relaunch();
        app.exit(0);
        return {};
    });

    ipcMain.handle('open-db-folder', () => {
        shell.openPath(path.dirname(currentDbPath));
    });

    ipcMain.handle('restart-app', () => {
        if (phpProcess) { phpProcess.kill(); phpProcess = null; }
        app.relaunch();
        app.exit(0);
    });

    ipcMain.handle('reset-db', async () => {
        const { response } = await dialog.showMessageBox(mainWindow, {
            type: 'warning',
            title: 'Reset Database',
            message: 'This will permanently delete ALL data and restart the app.',
            detail: 'Quotations, customers, and plans will be lost. This cannot be undone.',
            buttons: ['Cancel', 'Reset Everything'],
            defaultId: 0,
            cancelId: 0,
        });
        if (response !== 1) return { canceled: true };
        if (phpProcess) { phpProcess.kill(); phpProcess = null; }
        if (fs.existsSync(currentDbPath)) fs.unlinkSync(currentDbPath);
        app.relaunch();
        app.exit(0);
    });
}

// ── Auto updater ──────────────────────────────────────────────────────────────

function setupAutoUpdater() {
    if (!app.isPackaged) return;
    autoUpdater.autoDownload = true;
    autoUpdater.autoInstallOnAppQuit = true;
    autoUpdater.on('update-available', () => {
        dialog.showMessageBox({ type: 'info', title: 'Update Available',
            message: 'A new version is being downloaded in the background.', buttons: ['OK'] });
    });
    autoUpdater.on('update-downloaded', () => {
        dialog.showMessageBox({ type: 'info', title: 'Update Ready',
            message: 'Update downloaded. Restart now to install?',
            buttons: ['Restart Now', 'Later'], defaultId: 0,
        }).then(({ response }) => { if (response === 0) autoUpdater.quitAndInstall(); });
    });
    autoUpdater.on('error', err => console.error('[Updater]', err.message));
    autoUpdater.checkForUpdates();
}

// ── Boot ──────────────────────────────────────────────────────────────────────

app.whenReady().then(async () => {
    phpBin = findPhp();
    const laravelPath = getLaravelPath();
    currentDbPath = getDbPath();
    const machineCode = getMachineCode();
    const storagePath = getStoragePath();
    ensureDb(currentDbPath);
    ensureStorage(storagePath);

    try {
        runMigrations(phpBin, laravelPath, currentDbPath, machineCode, storagePath);
    } catch (e) {
        dialog.showErrorBox('Startup Error', 'Database migration failed:\n' + e.message);
        app.quit(); return;
    }

    setupIpc();
    startPhpServer(phpBin, laravelPath, currentDbPath, machineCode, storagePath);

    try {
        await waitForPort(PORT);
        createWindow();
        setupAutoUpdater();
    } catch (e) {
        dialog.showErrorBox('Startup Error', 'Failed to start server:\n' + e.message);
        app.quit();
    }
});

app.on('window-all-closed', () => {
    if (phpProcess) { phpProcess.kill(); phpProcess = null; }
    if (process.platform !== 'darwin') app.quit();
});
app.on('activate', () => { if (mainWindow === null) createWindow(); });
app.on('before-quit', () => { if (phpProcess) { phpProcess.kill(); phpProcess = null; } });
