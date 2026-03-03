document.addEventListener('DOMContentLoaded', function () {
    const chatWidget = document.getElementById('chat-widget');
    const chatToggleBtn = document.getElementById('chat-toggle-btn');
    const chatCloseBtn = document.getElementById('chat-close-btn');
    const chatMessages = document.getElementById('chat-messages');
    const chatInput = document.getElementById('chat-input');
    const chatSendBtn = document.getElementById('chat-send-btn');
    const chatTriggerLinks = document.querySelectorAll('.trigger-live-chat');

    // Toggle Chat Visibility
    function toggleChat() {
        chatWidget.classList.toggle('hidden');
        if (!chatWidget.classList.contains('hidden')) {
            chatInput.focus();
        }
    }

    if (chatToggleBtn) chatToggleBtn.addEventListener('click', toggleChat);
    if (chatCloseBtn) chatCloseBtn.addEventListener('click', toggleChat);

    // Open chat from any link with class 'trigger-live-chat'
    chatTriggerLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            chatWidget.classList.remove('hidden');
            chatInput.focus();
        });
    });

    // AI Knowledge Base
    const knowledgeBase = {
        greetings: {
            keywords: ['hello', 'hi', 'hey', 'start', 'morning', 'afternoon'],
            response: "Hello! 👋 I'm your VisionPro Virtual Assistant. Ask me about **returns**, **shipping**, **prices**, or **location**."
        },
        returns: {
            keywords: ['return', 'refund', 'warranty', 'exchange', 'broken', 'defective'],
            response: "We offer a **30-day return policy** for defective items. Premium OEM screens come with a Lifetime Warranty. <br><a href='return-policy.php' class='underline text-blue-200'>View full policy</a>."
        },
        shipping: {
            keywords: ['ship', 'delivery', 'track', 'time', 'long', 'arrive'],
            response: "Orders placed before 2 PM EST ship the same day! 🚚 We use FedEx and Canada Post. Free shipping on orders over $1,500."
        },
        location: {
            keywords: ['where', 'location', 'address', 'map', 'visit', 'pickup'],
            response: "We are located at: <br>**14 Automatic Rd, Unit #34, Brampton, ON**. <br>Open Mon-Fri 10am-7pm."
        },
        pricing: {
            keywords: ['price', 'cost', 'discount', 'wholesale', 'quote'],
            response: "Our prices are visible to registered wholesale members. <a href='signup.php' class='underline text-blue-200'>Create an account</a> to see live pricing."
        },
        contact: {
            keywords: ['human', 'person', 'agent', 'call', 'phone', 'talk', 'whatsapp', 'email', 'help'],
            response: "You can reach us directly via:<br><br><a href='https://wa.me/16474026699' target='_blank' class='inline-block w-full text-center bg-green-500 text-white px-4 py-2 rounded-lg font-bold hover:bg-green-600 transition-colors mb-2'>Chat on WhatsApp 💬</a><a href='mailto:info@visionprolcd.com' class='inline-block w-full text-center bg-orange-500 text-white px-4 py-2 rounded-lg font-bold hover:bg-orange-600 transition-colors'>Send an Email ✉️</a>"
        },
        samsung: {
            keywords: ['samsung', 'galaxy', 's23', 's24', 'oled'],
            response: "We stock premium Service Packs (OEM) and high-quality Aftermarket screens for Samsung. Check the <a href='products.php' class='underline text-blue-200'>Shop page</a> for stock."
        },
        iphone: {
            keywords: ['iphone', 'apple', 'screen', 'incell', 'soft'],
            response: "We carry XO7 (Premium), AQ7 (Standard), and Incell screens for all iPhone models. Highest quality in the market!"
        }
    };

    // Send Message Logic
    function sendMessage() {
        const message = chatInput.value.trim().toLowerCase();
        if (message) {
            // User Message
            appendMessage(chatInput.value, 'user'); // Keep original casing
            chatInput.value = '';

            // AI Processing
            setTimeout(() => {
                let foundMatch = false;

                // Check for keyword matches
                for (const [key, data] of Object.entries(knowledgeBase)) {
                    if (data.keywords.some(keyword => message.includes(keyword))) {
                        appendMessage(data.response, 'bot');
                        foundMatch = true;
                        break;
                    }
                }

                // Default Fallback
                if (!foundMatch) {
                    const fallback = "I'm not sure about that. 🤔 How would you like to connect?";
                    appendMessage(fallback, 'bot');
                    // auto-suggest Contact Options after fallback
                    setTimeout(() => appendMessage(knowledgeBase.contact.response, 'bot'), 500);
                }
            }, 600);
        }
    }

    if (chatSendBtn) {
        chatSendBtn.addEventListener('click', sendMessage);
    }

    if (chatInput) {
        chatInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
    }

    function appendMessage(html, sender) {
        const div = document.createElement('div');
        div.className = `max-w-[85%] p-3 rounded-2xl text-sm mb-2 shadow-sm ${sender === 'user'
                ? 'bg-primary-600 text-white ml-auto rounded-tr-none'
                : 'bg-gray-100 text-gray-800 mr-auto rounded-tl-none'
            }`;
        div.innerHTML = html;
        chatMessages.appendChild(div);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
});
