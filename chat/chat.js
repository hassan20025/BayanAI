const params = new URLSearchParams(window.location.search);
const chatId = params.get("chatId");

async function fetchUserChats() {
    try {
        const chatsContainer = document.querySelector(".sidebar-menu");
        const response = await fetch("http://localhost/BayanAI/api/chats/getChats.php", {
            credentials: "include",
        });
        if (!response.ok) throw new Error("Failed to fetch chats");
        const data = await response.json();
        data.data.forEach(chat => {
            chatsContainer.innerHTML += `
                <li data-chatId="${chat.id}" class="sidebar-menu-item ${chat.id === Number.parseInt(chatId) ? "active" : ""}">
                    <button  class="sidebar-menu-button ${chat.id === Number.parseInt(chatId) ? "active" : ""}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V3a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                        <span>${chat.title}</span>
                    </button>
                    <button data-chatId="${chat.id}" class="deleteChat">
                        <img height="20" width="20" src="../images/trash.svg">
                    </button>
                </li>
            `
        });
    } catch (err) {
        console.error(err);
    }
    addChatNavigationEvents();
    addChatDeleteEvent();
}
fetchUserChats();
const messagesContainer = document.getElementById("chatMessages");
async function fetchMessagesForChat(chatId) {
    
    try {
        const response = await fetch(
            `http://localhost/BayanAI/api/messages?chatId=${chatId}`,
            {
                credentials: "include",
            }
        );
        if (!response.ok) throw new Error("Failed to fetch messages");
        const messages = (await response.json()).data;
        messages.forEach(message => {
            if (message.role === "user") {
                messagesContainer.innerHTML += `
                    <div class="message user-message">
                        <div class="message-content">
                            <p>${message.content}</p>
                        </div>
                        <div class="message-avatar">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </div>
                    </div>
                `;
            }
            else {
                messagesContainer.innerHTML += `
                    <div class="message bot-message">
                        <div class="message-avatar">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M10 9V7a2 2 0 0 1 4 0v2"/>
                                <path d="M12 12h.01"/>
                                <path d="M12 17h.01"/>
                                <rect width="18" height="18" x="3" y="3" rx="2"/>
                            </svg>
                        </div>
                        <div class="message-content">
                            <p>${marked.parse(message.content)}</p>
                        </div>
                    </div>
                `;
            }
        });
        messagesContainer.scrollTop = messagesContainer.scrollHeight;

    } catch (err) {
        console.error(err);
    }
}

if (chatId) {
    fetchMessagesForChat(chatId);
}

async function handleSendMessage(e) {
    const chatInput = document.getElementById("chatInput");
    const message = chatInput.value.trim();
    if (!message) return;
  
    const urlParams = new URLSearchParams(window.location.search);
    const chatId = urlParams.get("chatId");
    
    e.target.disabled = true;
    try {
      let finalChatId = chatId;
      if (!chatId) {
        const formData = new URLSearchParams();
        formData.append("title", message);
  
        const createChatRes = await fetch("http://localhost/BayanAI/api/chats/createChat.php", {
          method: "POST",
          credentials: "include",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded"
          },
          body: formData
        });
  
        const createdChat = (await createChatRes.json()).data;
        finalChatId = createdChat.id;

        const messageFormData = new URLSearchParams();
        messageFormData.append("chatId", finalChatId);
        messageFormData.append("content", message);

        await fetch("http://localhost/BayanAI/api/messages/createMessage.php", {
            method: "POST",
            credentials: "include",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: messageFormData
        });

        const replyReqData = new URLSearchParams();
        replyReqData.append("question", message);
        replyReqData.append("chatId", finalChatId);

        const replyRes = await fetch("http://localhost/BayanAI/api/documentChunks/reply.php", {
            method: "POST",
            credentials: "include",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: replyReqData,
        });
        window.location.href = `?chatId=${finalChatId}`;
        return;
    }
  
      messagesContainer.innerHTML += `
        <div class="message user-message">
            <div class="message-content">
                <p>${message}</p>
            </div>
            <div class="message-avatar">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" 
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" 
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
            </div>
        </div>
    `;
    messagesContainer.scrollTop = messagesContainer.scrollHeight;

        const messageFormData = new URLSearchParams();
        messageFormData.append("chatId", finalChatId);
        messageFormData.append("content", message);

    await fetch("http://localhost/BayanAI/api/messages/createMessage.php", {
        method: "POST",
        credentials: "include",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: messageFormData
    });

    const replyReqData = new URLSearchParams();
    replyReqData.append("question", message);
    replyReqData.append("chatId", chatId);

    const replyRes = await fetch("http://localhost/BayanAI/api/documentChunks/reply.php", {
        method: "POST",
        credentials: "include",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: replyReqData,
    });

    const reply = await replyRes.json();

    messagesContainer.innerHTML += `
        <div class="message bot-message">
            <div class="message-avatar">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10 9V7a2 2 0 0 1 4 0v2"/>
                    <path d="M12 12h.01"/>
                    <path d="M12 17h.01"/>
                    <rect width="18" height="18" x="3" y="3" rx="2"/>
                </svg>
            </div>
            <div class="message-content">
                ${marked.parse(reply.data)}
            </div>
        </div> 
    `;
    messagesContainer.scrollTop = messagesContainer.scrollHeight;

    chatInput.value = "";
    } catch (err) {
        console.error("Failed to send message", err);
    }
    finally {
        e.target.disabled = false;
    }
}


document.getElementById("sendMessageButton").addEventListener("click", handleSendMessage);

function addChatNavigationEvents() {
    const chatButtons = document.querySelectorAll(".sidebar-menu-item");
    if (chatId) {
        chatButtons.forEach(b => {
            if (b.dataset.chatid !== chatId) {
                b.classList.remove("active");
                // b.parentElement.classList.remove("active");
            }
        });
    }

    chatButtons.forEach((btn) => {
        if (btn.id === "openUploadModalButton") return;
        btn.addEventListener("click", (e) => {
            const chatId = btn.dataset.chatid;
            chatButtons.forEach(b => {
                b.classList.remove("active");
            });
            btn.classList.add("active");
            window.location.href = `?chatId=${chatId || ""}`;
        });
    });
}

async function handleDelete(id) {
    if (id === chatId) {
        window.location.href = "/bayanai/chat";
    }
    const formData = new URLSearchParams();
    formData.append("chatId", id);
    await fetch("http://localhost/BayanAI/api/chats/deleteChat.php", {
        method: "POST",
        credentials: "include",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: formData,
    });
}

function addChatDeleteEvent() {
    const deleteBtns = document.querySelectorAll(".deleteChat");
    deleteBtns.forEach(btn => {
        btn.addEventListener("click", (e) => {
            e.stopPropagation();
            btn.parentElement.remove();
            handleDelete(btn.dataset.chatid);
        });
    });
}

chatInput.addEventListener("keypress", (e) => {
    if (e.key === "Enter") {
        handleSendMessage(e);
    }
});

async function toggleUploadButton() {
    const openUploadModalButton = document.getElementById("openUploadModalButton"); 
    const response = await fetch("http://localhost/BayanAI/api/users/me.php", {
        credentials: "include",
    });

    if (!response.ok) {
        throw new Error("Not authenticated");
    }

    const user = await response.json();

    if (!user.data.canUpload) {
        openUploadModalButton.style.display = "none";
    }
    else {
        openUploadModalButton.style.display = "flex";
    }

}

toggleUploadButton();