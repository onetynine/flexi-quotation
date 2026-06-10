@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto" x-data="settingsPage()" x-init="init()">
    <h1 class="text-xl font-bold text-gray-800 mb-6">Settings</h1>

    {{-- Desktop-only notice --}}
    <div x-show="!isElectron" class="bg-yellow-50 border border-yellow-300 text-yellow-800 px-4 py-3 rounded mb-6 text-sm">
        Settings require the desktop app. Some options are unavailable in the browser.
    </div>

    {{-- Station Code --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-700">Station Code</h2>
        </div>
        <div class="px-5 py-4 space-y-3">
            <p class="text-sm text-gray-500">
                2-character code identifying this station/user in quotation numbers.<br>
                Format: <code class="bg-gray-100 px-1 rounded text-xs font-mono">SR<strong x-text="machineCode || 'MV'"></strong>2601<span class="text-gray-400">03</span></code>
                &nbsp;<span class="text-xs text-gray-400">(SR + code + YYMM + sequence)</span>
            </p>
            <div x-show="isElectron" class="flex items-center gap-2">
                <input x-model="machineCodeInput" type="text" maxlength="2"
                       @input="machineCodeInput = machineCodeInput.toUpperCase().replace(/[^A-Z0-9]/g,'')"
                       class="w-16 border border-gray-300 rounded px-3 py-2 text-sm font-mono uppercase tracking-widest text-center focus:outline-none focus:ring-2 focus:ring-yellow-400"
                       placeholder="MV">
                <button @click="saveMachineCode()" :disabled="busy"
                        class="bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-semibold text-sm px-4 py-2 rounded transition disabled:opacity-50">
                    Save
                </button>
                <span class="text-xs text-gray-400">Current: <strong x-text="machineCode"></strong></span>
            </div>
            <div x-show="!isElectron" class="text-sm text-gray-400 italic">Requires desktop app.</div>
            <p class="text-xs text-gray-400">Exactly 2 alphanumeric characters. Only affects future quotations.</p>

            <div x-show="codeMessage" class="text-sm rounded px-3 py-2"
                 :class="codeMessageOk ? 'bg-green-50 border border-green-200 text-green-800' : 'bg-red-50 border border-red-200 text-red-800'"
                 x-text="codeMessage"></div>
        </div>
    </div>

    {{-- Database --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-700">Database</h2>
        </div>
        <div class="px-5 py-4 space-y-4">
            <div>
                <div class="text-xs text-gray-500 mb-1">Current location</div>
                <div class="flex items-center gap-2">
                    <code class="flex-1 bg-gray-50 border border-gray-200 rounded px-3 py-2 text-xs text-gray-700 break-all" x-text="dbPath || '—'"></code>
                    <button x-show="isElectron" @click="openFolder()"
                            class="shrink-0 text-xs px-3 py-2 border border-gray-300 rounded hover:bg-gray-50 transition">
                        Open Folder
                    </button>
                </div>
            </div>

            <div x-show="pendingPath" class="bg-blue-50 border border-blue-200 rounded px-3 py-2 text-sm text-blue-800">
                Pending change: <strong x-text="pendingPath"></strong>
                <br><span class="text-xs">Restart the app to apply.</span>
                <div class="mt-2 flex gap-2">
                    <button @click="restart()"
                            class="bg-blue-600 text-white text-xs px-3 py-1.5 rounded hover:bg-blue-700 transition">
                        Restart Now
                    </button>
                    <button @click="pendingPath = ''"
                            class="text-xs px-3 py-1.5 border border-blue-300 rounded hover:bg-blue-100 transition">
                        Later
                    </button>
                </div>
            </div>

            <div x-show="isElectron" class="flex gap-3 flex-wrap">
                <button @click="changeLocation()" :disabled="busy"
                        class="bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-semibold text-sm px-4 py-2 rounded transition disabled:opacity-50">
                    Change Location
                </button>
                <button @click="resetDb()" :disabled="busy"
                        class="bg-red-600 hover:bg-red-700 text-white font-semibold text-sm px-4 py-2 rounded transition disabled:opacity-50">
                    Reset Database
                </button>
            </div>

            <div x-show="dbVersion" class="text-xs text-gray-400">App version: <span x-text="dbVersion"></span></div>
        </div>
    </div>

    {{-- Backup & Restore --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-700">Backup &amp; Restore</h2>
        </div>
        <div class="px-5 py-4 space-y-4">
            <p class="text-sm text-gray-500">Backup saves all quotations, customers, and plans to a file. Restore replaces the current database — the app will restart.</p>

            <div x-show="!isElectron" class="text-sm text-gray-400 italic">Requires desktop app.</div>

            <div x-show="isElectron" class="flex gap-3">
                <button @click="backup()" :disabled="busy"
                        class="bg-gray-800 hover:bg-gray-700 text-white font-semibold text-sm px-4 py-2 rounded transition disabled:opacity-50">
                    Backup Now
                </button>
                <button @click="restore()" :disabled="busy"
                        class="bg-white hover:bg-gray-50 text-gray-700 font-semibold text-sm px-4 py-2 rounded border border-gray-300 transition disabled:opacity-50">
                    Restore Backup
                </button>
            </div>

            <div x-show="message" class="text-sm rounded px-3 py-2"
                 :class="messageType === 'success' ? 'bg-green-50 border border-green-200 text-green-800' : 'bg-red-50 border border-red-200 text-red-800'"
                 x-text="message"></div>
        </div>
    </div>
</div>

<script>
function settingsPage() {
    return {
        isElectron: false,
        dbPath: '',
        dbVersion: '',
        machineCode: '',
        machineCodeInput: '',
        pendingPath: '',
        busy: false,
        message: '',
        messageType: 'success',
        codeMessage: '',
        codeMessageOk: true,

        async init() {
            this.isElectron = !!window.electronAPI;
            if (!this.isElectron) return;
            const info = await window.electronAPI.getDbInfo();
            this.dbPath = info.path;
            this.dbVersion = info.version;
            this.machineCode = info.machineCode;
            this.machineCodeInput = info.machineCode;
        },

        async saveMachineCode() {
            const val = this.machineCodeInput.trim().toUpperCase();
            if (val.length !== 2) { this.codeMessage = 'Must be exactly 2 characters.'; this.codeMessageOk = false; return; }
            this.busy = true; this.codeMessage = '';
            const result = await window.electronAPI.setMachineCode(val);
            this.busy = false;
            if (result.error) { this.codeMessage = result.error; this.codeMessageOk = false; return; }
            this.machineCode = result.code;
            this.machineCodeInput = result.code;
            this.codeMessageOk = true;
            this.codeMessage = 'Saved. New quotations will use code: ' + result.code;
        },

        async changeLocation() {
            this.busy = true;
            const result = await window.electronAPI.changeDbPath();
            this.busy = false;
            if (result.canceled) return;
            this.pendingPath = result.path;
        },

        async openFolder() {
            await window.electronAPI.openDbFolder();
        },

        async backup() {
            this.busy = true; this.message = '';
            const result = await window.electronAPI.backupDb();
            this.busy = false;
            if (result.canceled) return;
            this.messageType = 'success';
            this.message = 'Backup saved to: ' + result.path;
        },

        async restore() {
            if (!confirm('Restore will replace ALL current data and restart the app. Continue?')) return;
            this.busy = true; this.message = '';
            await window.electronAPI.restoreDb();
        },

        restart() {
            window.electronAPI.restartApp();
        },

        async resetDb() {
            const result = await window.electronAPI.resetDb();
            if (result && result.canceled) return;
            // app will relaunch — nothing else to do
        },
    }
}
</script>
@endsection
