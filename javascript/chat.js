const form = document.querySelector(".typing-area"),
    incoming_id = form.querySelector(".incoming_id").value,
    inputField = form.querySelector(".input-field"),
    sendBtn = form.querySelector(".send-btn"),
    chatBox = document.querySelector(".chat-box"),
    deleteBtn = document.querySelector(".delete-btn");

inputField.focus();

inputField.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
        e.preventDefault(); // Verhindere das Standard-Verhalten der Enter-Taste
        if (inputField.value.trim() !== "") {
            sendMessage();
        }
    }
});

inputField.addEventListener('input', () => {
    if (inputField.value.trim() !== "") {
        sendBtn.classList.add("active");
    } else {
        sendBtn.classList.remove("active");
    }
});

function sendMessage() {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "php/insert-chat.php", true);
    xhr.onload = () => {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                inputField.value = "";
                sendBtn.classList.remove("active");
                updateDeleteButton();
            }
        }
    }
    let formData = new FormData(form);
    xhr.send(formData);
}

sendBtn.onclick = sendMessage; // Setze sendMessage als onClick Event für den Send-Button

form.onsubmit = (e) => {
    e.preventDefault(); // Verhindere das Standard-Submit-Verhalten des Formulars
};

function updateDeleteButton() {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "php/check-messages.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = () => {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                let messagesExist = xhr.responseText === 'true';
                if (messagesExist) {
                    deleteBtn.classList.add("active");
                    deleteBtn.disabled = false;
                } else {
                    deleteBtn.classList.remove("active");
                    deleteBtn.disabled = true;
                }
            }
        }
    }
    xhr.send("incoming_id=" + incoming_id);
}

setInterval(() => {
    updateChat();
    // Entferne updateDeleteButton von hier, da es in updateChat aufgerufen wird
}, 500);

function scrollToBottom() {
    chatBox.scrollTop = chatBox.scrollHeight;
}

function initialChatLoad() {
    updateChat(); // Aktualisiere den Chat beim ersten Laden
    setTimeout(() => {
        scrollToBottom(); // Scrollen Sie nach unten, nachdem die Nachrichten geladen wurden
    }, 500); // Warten Sie kurz, um sicherzustellen, dass die Nachrichten geladen wurden
}

// Rufen Sie initialChatLoad beim Laden der Seite auf
window.onload = initialChatLoad;

function updateChat() {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "php/get-chat.php", true);
    xhr.onload = () => {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                chatBox.innerHTML = xhr.response;
                updateDeleteButton(); // Aktualisiere den Zustand des Löschen-Buttons
            }
        }
    }
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send("incoming_id=" + incoming_id);
}

deleteBtn.addEventListener('click', () => {
    if (deleteBtn.disabled === false) {
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "php/delete-chat.php", true);
        xhr.onload = () => {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    chatBox.innerHTML = "";
                    deleteBtn.classList.remove("active");
                    deleteBtn.disabled = true;
                }
            }
        }
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send("incoming_id=" + incoming_id);
    }
});



chatBox.onmouseenter = () => {
    chatBox.classList.add("active");
}

chatBox.onmouseleave = () => {
    chatBox.classList.remove("active");
}


setInterval(() =>{
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "php/get-chat.php", true);
    xhr.onload = ()=>{
      if(xhr.readyState === XMLHttpRequest.DONE){
          if(xhr.status === 200){
            let data = xhr.response;
            chatBox.innerHTML = data;
          }
      }
    }
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send("incoming_id="+incoming_id);
}, 500);




document.querySelector(".typing-area").addEventListener("submit", function(e) {
    e.preventDefault();
    const messageInput = this.querySelector("input[name='message']");
    const incoming_id = this.querySelector("input[name='incoming_id']").value;
    const message = messageInput.value;

    if(message !== "") {
        // Senden der Nachricht an Ihren Server
        const httpRequest = new XMLHttpRequest();
        httpRequest.open("POST", "php/sendDiscordNotification.php", true); // Ersetzen Sie mit dem tatsächlichen Pfad Ihrer Nachrichtenverarbeitungs-PHP-Datei
        httpRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        httpRequest.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                // Verarbeitung der Antwort vom Server
                // Hier können Sie prüfen, ob die Nachricht erfolgreich gespeichert wurde,
                // und basierend darauf entscheiden, ob der Discord-Webhook ausgelöst werden soll.
                
                // Wenn die Nachricht erfolgreich gespeichert wurde, Webhook an Discord senden
                if(this.responseText === "success") {
                    sendDiscordNotification(incoming_id);
                }
            }
        };
        httpRequest.send("incoming_id=" + incoming_id + "&message=" + encodeURIComponent(message));

        // Zurücksetzen des Eingabefeldes
        messageInput.value = "";
    }
});

function sendDiscordNotification(incoming_id) {
    const httpRequest = new XMLHttpRequest();
    httpRequest.open("POST", "php/sendDiscordNotification.php", true);
    httpRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    httpRequest.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            // Verarbeitung der Antwort vom Webhook-Skript
            console.log("Discord Notification sent:", this.responseText);
        }
    };
    httpRequest.send("incoming_id=" + incoming_id);
}
