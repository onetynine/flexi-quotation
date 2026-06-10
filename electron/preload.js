const { contextBridge, ipcRenderer } = require('electron');

contextBridge.exposeInMainWorld('electronAPI', {
    getDbInfo:        () => ipcRenderer.invoke('get-db-info'),
    setMachineCode:   (code) => ipcRenderer.invoke('set-machine-code', code),
    changeDbPath:     () => ipcRenderer.invoke('change-db-path'),
    backupDb:         () => ipcRenderer.invoke('backup-db'),
    restoreDb:        () => ipcRenderer.invoke('restore-db'),
    openDbFolder:     () => ipcRenderer.invoke('open-db-folder'),
    restartApp:       () => ipcRenderer.invoke('restart-app'),
    resetDb:          () => ipcRenderer.invoke('reset-db'),
});
