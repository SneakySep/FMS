<?php
/**
 * Freight Quote Modal (Fillable).
 */
?>
<div id="quoteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto transform scale-95 transition-all duration-300">
        <form id="quoteForm">
            <!-- Close Button & Title -->
            <div class="sticky top-0 bg-white p-6 border-b border-slate-100 flex justify-between items-center z-10">
                <h2 class="text-xl font-bold text-slate-900">New Freight Quote</h2>
                <button type="button" onclick="closeQuoteModal()" class="text-slate-400 hover:text-red-600 transition-colors p-2 hover:bg-red-50 rounded-lg">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>



            <div class="p-8 space-y-8">
                <!-- Header: Company Info -->
                <div class="flex justify-between items-start">
                    <div class="space-y-2">
                        <h1 class="text-2xl font-bold text-slate-900">Priority Handling</h1>
                        <p class="text-sm text-slate-600">1618-B Copernico St., Makati City</p>
                    </div>
                    <div class="w-32 h-20 flex items-center justify-center">
                        <img src="/assets/image/logo.png" alt="Logo" class="max-h-full max-w-full object-contain">
                    </div>
                </div>

                <!-- Bill To -->
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-4">Bill To</h3>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Customer Name</label>
                            <input type="text" name="customer_name" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" placeholder="Enter customer name" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Customer Address</label>
                            <input type="text" name="customer_address" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" placeholder="Enter customer address">
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto border border-slate-200 rounded-xl">
                    <table class="w-full text-sm" id="quoteTable">
                        <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4 text-center w-20">QTY</th>
                                <th class="px-6 py-4">Description</th>
                                <th class="px-6 py-4 text-right">Unit Price</th>
                                <th class="px-6 py-4 text-right">Amount</th>
                                <th class="px-6 py-4 w-16"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100" id="quoteTableBody">
                            <tr>
                                <td class="px-6 py-4"><input type="number" name="qty[]" class="w-16 bg-white border border-slate-200 rounded-lg px-2 py-1 text-center text-sm focus:ring-2 focus:ring-blue-500" value="1" onchange="calculateTotal()"></td>
                                <td class="px-6 py-4"><input type="text" name="desc[]" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500" placeholder="Enter item description"></td>
                                <td class="px-6 py-4"><input type="number" name="price[]" class="w-24 bg-white border border-slate-200 rounded-lg px-3 py-1.5 text-right text-sm focus:ring-2 focus:ring-blue-500" value="0.00" onchange="calculateTotal()"></td>
                                <td class="px-6 py-4 text-right font-semibold text-slate-900">$0.00</td>
                                <td class="px-6 py-4 text-center"><button type="button" class="text-red-500 hover:text-red-700 transition-colors" onclick="removeRow(this)"><i class="fa-solid fa-trash"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="p-4 bg-slate-50 border-t border-slate-200">
                        <button type="button" class="text-sm text-blue-600 font-semibold hover:text-blue-800 flex items-center gap-2" onclick="addRow()"><i class="fa-solid fa-plus-circle"></i> Add Item</button>
                    </div>
                </div>


                <!-- Totals -->
                <div class="flex justify-end">
                    <div class="w-64 space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600">Subtotal</span>
                            <span class="font-medium text-slate-900" id="subtotal">$0.00</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold border-t border-slate-200 pt-3">
                            <span class="text-slate-900">Total (USD)</span>
                            <span class="text-blue-600" id="grandTotal">$0.00</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Button -->
            <div class="p-6 border-t border-slate-100 flex justify-end gap-3 bg-slate-50 rounded-b-2xl">
                <button type="button" onclick="closeQuoteModal()" class="px-6 py-2 rounded-lg text-slate-600 font-semibold hover:bg-slate-200 transition-colors">Cancel</button>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-700 shadow-lg shadow-blue-600/20 transition-all">Save & Send Quote</button>
            </div>
        </form>
    </div>
</div>

<script>
    function closeQuoteModal() {
        const modal = document.getElementById('quoteModal');
        modal.classList.add('opacity-0', 'pointer-events-none');
        modal.querySelector('div').classList.add('scale-95');
    }

    function addRow() {
        const body = document.getElementById('quoteTableBody');
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="px-6 py-4"><input type="number" name="qty[]" class="w-16 border rounded p-1 text-center" value="1" onchange="calculateTotal()"></td>
            <td class="px-6 py-4"><input type="text" name="desc[]" class="w-full border rounded p-1" placeholder="Description"></td>
            <td class="px-6 py-4"><input type="number" name="price[]" class="w-24 border rounded p-1 text-right" value="0.00" onchange="calculateTotal()"></td>
            <td class="px-6 py-4 text-right font-semibold text-slate-900">$0.00</td>
            <td class="px-6 py-4"><button type="button" class="text-red-500" onclick="removeRow(this)">Delete</button></td>
        `;
        body.appendChild(row);
    }

    function removeRow(btn) {
        btn.closest('tr').remove();
        calculateTotal();
    }

    function calculateTotal() {
        let subtotal = 0;
        const rows = document.querySelectorAll('#quoteTableBody tr');
        rows.forEach(row => {
            const qty = row.querySelector('[name="qty[]"]').value;
            const price = row.querySelector('[name="price[]"]').value;
            const amount = qty * price;
            row.querySelector('td:nth-child(4)').innerText = '$' + amount.toFixed(2);
            subtotal += amount;
        });
        document.getElementById('subtotal').innerText = '$' + subtotal.toFixed(2);
        document.getElementById('grandTotal').innerText = '$' + subtotal.toFixed(2);
    }
</script>
