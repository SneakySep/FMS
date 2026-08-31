document.addEventListener('DOMContentLoaded', () => {
    console.log('Logs dashboard initialized');
    // TODO: Fetch logs from API and populate table
    const logsTableBody = document.getElementById('logsTableBody');
    logsTableBody.innerHTML = `
        <tr>
            <td class="p-4">2026-08-31 13:45:01</td>
            <td class="p-4 font-semibold text-slate-800">Admin_John</td>
            <td class="p-4">Modified customer #1024 data</td>
            <td class="p-4 text-slate-400">192.168.1.5</td>
            <td class="p-4">
                <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">SUCCESS</span>
            </td>
        </tr>
    `;
});
