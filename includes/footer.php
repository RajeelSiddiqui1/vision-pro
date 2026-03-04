<?php
// includes/footer.php
?>
<footer class="bg-white border-t border-gray-100 pt-20 pb-10">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
            <!-- Brand -->
            <div class="space-y-6">
                <!-- Brand Info -->
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-lg">V</span>
                    </div>
                    <span class="text-xl font-bold text-gray-900 tracking-tight">VisionPro</span>
                </div>
                <p class="text-gray-500 leading-relaxed text-sm">
                    VisionPro LCD Refurbishing Inc. is Mississauga's leading wholesaler for high-quality mobile phone parts and professional repair tools.
                </p>
                
                <!-- Newsletter Form -->
                <form action="newsletter.php" method="POST" class="mt-4">
                    <label class="block text-xs font-bold text-gray-900 uppercase tracking-widest mb-2">Subscribe to our Newsletter</label>
                    <div class="flex gap-2">
                        <input type="email" name="email" required placeholder="Enter your email" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary-500 outline-none text-sm">
                        <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                    <?php if(isset($_GET['newsletter']) && $_GET['newsletter'] == 'success'): ?>
                        <p class="text-green-600 text-xs mt-2 font-bold">✓ Subscribed successfully!</p>
                    <?php endif; ?>
                </form>

                <div class="flex gap-4 pt-2">
                    <a href="#" class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 hover:bg-primary-600 hover:text-white transition-all">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                    </a>
                </div>
            </div>

            <!-- Company -->
            <div>
                <h4 class="font-bold text-gray-900 mb-6 uppercase text-xs tracking-widest">Company</h4>
                <ul class="space-y-4">
                    <li><a href="about.php" class="text-gray-500 hover:text-primary-600 text-sm transition-colors">About Us</a></li>
                    <li><a href="contact.php" class="text-gray-500 hover:text-primary-600 text-sm transition-colors">Contact</a></li>
                    <li><a href="location.php" class="text-gray-500 hover:text-primary-600 text-sm transition-colors">Location & Hours</a></li>
                    <li><a href="blog.php" class="text-gray-500 hover:text-primary-600 text-sm transition-colors">Blog</a></li>
                    <li><a href="marketing-material.php" class="text-gray-500 hover:text-primary-600 text-sm transition-colors">Marketing Assets</a></li>
                </ul>
            </div>

            <!-- Services -->
            <div>
                <h4 class="font-bold text-gray-900 mb-6 uppercase text-xs tracking-widest">Services</h4>
                <ul class="space-y-4">
                    <li><a href="services.php" class="text-gray-500 hover:text-primary-600 text-sm transition-colors">All Services</a></li>
                    <li><a href="lcd-buyback.php" class="text-gray-500 hover:text-primary-600 text-sm transition-colors">LCD Buyback</a></li>
                    <li><a href="pre-owned.php" class="text-gray-500 hover:text-primary-600 text-sm transition-colors">Pre-Owned Devices</a></li>
                    <li><a href="brands.php" class="text-gray-500 hover:text-primary-600 text-sm transition-colors">Our Brands</a></li>
                    <li><a href="api-consumers.php" class="text-gray-500 hover:text-primary-600 text-sm transition-colors">API Access</a></li>
                </ul>
            </div>

            <!-- Support -->
            <div>
                <h4 class="font-bold text-gray-900 mb-6 uppercase text-xs tracking-widest">Support</h4>
                <ul class="space-y-4">
                    <li><a href="#" class="text-gray-500 hover:text-primary-600 text-sm transition-colors trigger-live-chat">Live Chat</a></li>
                    <li><a href="faq.php" class="text-gray-500 hover:text-primary-600 text-sm transition-colors">FAQs</a></li>
                    <li><a href="terms.php" class="text-gray-500 hover:text-primary-600 text-sm transition-colors">Terms of Service</a></li>
                    <li><a href="privacy.php" class="text-gray-500 hover:text-primary-600 text-sm transition-colors">Privacy Policy</a></li>
                    <li><a href="return-policy.php" class="text-gray-500 hover:text-primary-600 text-sm transition-colors">Return Policy</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div>
                <h4 class="font-bold text-gray-900 mb-6 uppercase text-xs tracking-widest">Contact Us</h4>
                <ul class="space-y-4">
                    <li class="flex gap-3">
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="text-gray-500 text-sm">7215 Goreway Dr #1c27, Mississauga, L4T2T9, Ontario</span>
                    </li>
                    <li class="flex gap-3">
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <span class="text-gray-500 text-sm">647-402-6699</span>
                    </li>
                    <li class="flex gap-3">
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span class="text-gray-500 text-sm">Visionpro.lcd@gmail.com</span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="pt-10 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center gap-6">
            <p class="text-gray-400 text-xs">© <?= date('Y') ?> VisionPro LCD Refurbishing Inc. All rights reserved.</p>
            <div class="flex gap-6">
                <!-- Payment Icons -->
                <div class="h-6 w-10 bg-gray-100 rounded"></div>
                <div class="h-6 w-10 bg-gray-100 rounded"></div>
                <div class="h-6 w-10 bg-gray-100 rounded"></div>
            </div>
        </div>
    </div>
</footer>
    <!-- Live Chat Widget -->
    <div id="chat-widget" class="fixed bottom-24 right-6 w-80 bg-white rounded-2xl shadow-2xl border border-gray-100 hidden z-50 flex flex-col overflow-hidden transition-all duration-300">
        <div class="bg-gradient-to-br from-primary-500 to-primary-700 p-4 flex justify-between items-center text-white">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center text-sm">🤖</div>
                <div>
                    <h3 class="font-bold text-sm">VisionPro AI</h3>
                    <p class="text-xs text-primary-100 flex items-center gap-1"><span class="w-1.5 h-1.5 bg-green-400 rounded-full"></span> Online</p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="mailto:Visionpro.lcd@gmail.com" title="Send Email" class="hover:bg-white/20 p-1 rounded-lg transition-colors flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </a>
                <a href="https://wa.me/16474026699" target="_blank" title="Switch to WhatsApp" class="hover:bg-white/20 p-1 rounded-lg transition-colors flex items-center justify-center">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.463 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                </a>
                <button id="chat-close-btn" class="hover:bg-white/20 p-1 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <!-- Messages -->
        <div id="chat-messages" class="h-80 overflow-y-auto p-4 bg-gray-50 flex flex-col gap-2">
            <!-- Bot Welcome -->
            <div class="bg-gray-100 text-gray-800 p-3 rounded-2xl rounded-tl-none max-w-[80%] text-sm mr-auto">
                Hello! 👋 Welcome to VisionPro. How can we verify your wholesale account today?
            </div>
        </div>

        <!-- Input -->
        <div class="p-3 border-t border-gray-100 bg-white">
            <div class="flex gap-2">
                <input type="text" id="chat-input" placeholder="Type a message..." class="flex-1 bg-gray-50 border-none rounded-xl px-4 py-2 text-sm focus:ring-1 focus:ring-primary-500">
                <button id="chat-send-btn" class="bg-gradient-to-br from-primary-500 to-primary-700 text-white p-2 rounded-xl hover:from-primary-600 hover:to-primary-800 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </button>
            </div>
            <div class="text-[10px] text-center text-gray-400 mt-2">Powered by VisionPro</div>
        </div>
    </div>

    <!-- Floating Toggle Button -->
    <button id="chat-toggle-btn" class="fixed bottom-6 right-6 w-14 h-14 bg-gradient-to-br from-primary-500 to-primary-700 text-white rounded-full shadow-2xl flex items-center justify-center hover:from-primary-600 hover:to-primary-800 hover:scale-105 transition-all z-50 group">
        <svg class="w-7 h-7 group-hover:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
        <svg class="w-7 h-7 hidden group-hover:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>

    <script src="assets/js/main.js"></script>
    <script src="assets/js/chat.js"></script>


