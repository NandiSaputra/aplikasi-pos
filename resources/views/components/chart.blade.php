
<div class="w-full lg:w-96 bg-gray-50 p-8 flex flex-col justify-between rounded-bl-3xl lg:rounded-bl-none rounded-br-3xl lg:rounded-tr-3xl">
 <h2 class="text-3xl font-bold text-gray-800 mb-6">Order</h2>

 <!-- Order Items List -->
 <div class="flex-1 overflow-y-auto custom-scrollbar mb-6">
     <!-- Example Order Item 1 -->
     <div class="flex items-center justify-between py-3 border-b border-gray-200">
         <div>
             <h3 class="font-semibold text-gray-900">Fish Fillet Burger</h3>
             <p class="text-sm text-gray-500">Extra Cheese</p>
             <p class="text-sm text-gray-500">No vegetables</p>
         </div>
         <div class="flex items-center space-x-2">
             <span class="font-bold text-gray-800">$25</span>
             <div class="flex items-center ml-4">
                 <button class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition-colors">-</button>
                 <span class="mx-2 font-semibold text-lg">2</span>
                 <button class="w-8 h-8 flex items-center justify-center rounded-lg bg-orange-500 text-white hover:bg-orange-600 transition-colors">+</button>
             </div>
         </div>
     </div>

     <!-- Example Order Item 2 -->
     <div class="flex items-center justify-between py-3 border-b border-gray-200">
         <div>
             <h3 class="font-semibold text-gray-900">Mexican Burger</h3>
             <p class="text-sm text-gray-500">No onion</p>
             <p class="text-sm text-gray-500">No vegetables</p>
         </div>
         <div class="flex items-center space-x-2">
             <span class="font-bold text-gray-800">$32</span>
             <div class="flex items-center ml-4">
                 <button class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition-colors">-</button>
                 <span class="mx-2 font-semibold text-lg">1</span>
                 <button class="w-8 h-8 flex items-center justify-center rounded-lg bg-orange-500 text-white hover:bg-orange-600 transition-colors">+</button>
             </div>
         </div>
     </div>

     <!-- Example Order Item 3 -->
     <div class="flex items-center justify-between py-3 border-b border-gray-200">
         <div>
             <h3 class="font-semibold text-gray-900">Quinoa Black Bean Burger</h3>
             <p class="text-sm text-gray-500">Mozzarella</p>
             <p class="text-sm text-gray-500">Extra spicy</p>
         </div>
         <div class="flex items-center space-x-2">
             <span class="font-bold text-gray-800">$29</span>
             <div class="flex items-center ml-4">
                 <button class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition-colors">-</button>
                 <span class="mx-2 font-semibold text-lg">1</span>
                 <button class="w-8 h-8 flex items-center justify-center rounded-lg bg-orange-500 text-white hover:bg-orange-600 transition-colors">+</button>
             </div>
         </div>
     </div>
 </div>

 <!-- Totals -->
 <div class="space-y-4 mb-8">
     <div class="flex justify-between items-center text-lg text-gray-700">
         <span>Sub Total</span>
         <span class="font-semibold">$111</span>
     </div>
     <div class="flex justify-between items-center text-lg text-gray-700">
         <span>VAT (10%)</span>
         <span class="font-semibold">$11.1</span>
     </div>
     <div class="flex justify-between items-center text-2xl font-bold text-gray-900 border-t pt-4 border-gray-200">
         <span>TOTAL</span>
         <span class="text-orange-600">$122.1</span>
     </div>
 </div>

 <!-- Place Order Button -->
 <button class="w-full py-4 rounded-xl bg-orange-500 text-white text-xl font-bold shadow-lg hover:bg-orange-600 transition-colors">
     PLACE ORDER
 </button>
</div>
