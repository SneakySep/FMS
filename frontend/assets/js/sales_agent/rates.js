
document.addEventListener('DOMContentLoaded', () => {
    const rateSearchForm = document.getElementById('rateSearchForm');
    const tableBody = document.querySelector('tbody');

    if (rateSearchForm) {
        rateSearchForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            // Show loading state
            tableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-slate-500">
                        <div class="flex flex-col items-center">
                            <div class="w-8 h-8 border-4 border-blue-500 border-t-transparent rounded-full animate-spin mb-3"></div>
                            Searching for rates...
                        </div>
                    </td>
                </tr>
            `;

            // Simulate API call
            await new Promise(resolve => setTimeout(resolve, 1500));

            // Populate with mock results
            tableBody.innerHTML = `
                <tr class="hover:bg-slate-50 transition-colors animate-in fade-in duration-500">
                    <td class="px-6 py-4 font-medium text-slate-900">OceanLine Express</td>
                    <td class="px-6 py-4">FCL - Standard</td>
                    <td class="px-6 py-4">12-14 Days</td>
                    <td class="px-6 py-4 font-bold text-blue-600">$2,450</td>
                    <td class="px-6 py-4 text-right">
                        <button class="bg-blue-50 text-blue-700 hover:bg-blue-100 font-medium py-1.5 px-3 rounded-md transition-colors">
                            Book Now
                        </button>
                    </td>
                </tr>
                <tr class="hover:bg-slate-50 transition-colors animate-in fade-in duration-500">
                    <td class="px-6 py-4 font-medium text-slate-900">AirSpeed Cargo</td>
                    <td class="px-6 py-4">Air Freight - Express</td>
                    <td class="px-6 py-4">2-3 Days</td>
                    <td class="px-6 py-4 font-bold text-blue-600">$5,800</td>
                    <td class="px-6 py-4 text-right">
                        <button class="bg-blue-50 text-blue-700 hover:bg-blue-100 font-medium py-1.5 px-3 rounded-md transition-colors">
                            Book Now
                        </button>
                    </td>
                </tr>
            `;
        });
    }
});
