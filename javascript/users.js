const searchBar = document.querySelector(".search input"),
      searchIcon = document.querySelector(".search button"),
      usersList = document.querySelector(".users-list"),
      searchBtn = document.getElementById("searchBtn"),
      chatsBtn = document.getElementById("chatsBtn");

let currentTab = 'search'; // Aktueller Tab

searchIcon.onclick = () => {
  searchBar.classList.toggle("show");
  searchBar.focus();
  searchBar.value = "";
  if (currentTab === 'search') {
    usersList.innerHTML = '';
  } else if (currentTab === 'chats') {
    loadChats(); // Lädt die Chatliste, wenn der Benutzer auf den Such-Icon klickt und der aktuelle Tab "chats" ist
  }
};

searchBar.onkeyup = () => {
  let searchTerm = searchBar.value;
  if (searchTerm != "") {
    if (currentTab === 'search') {
      searchUsers(searchTerm);
    } else if (currentTab === 'chats') {
      searchChats(searchTerm);
    }
  } else {
    if (currentTab === 'chats') {
      loadChats(); // Lädt die Chatliste erneut, wenn der Suchbegriff entfernt wird
    } else {
      usersList.innerHTML = '';
    }
  }
};

// Funktion zum Umschalten der Anzeige wichtiger Benutzer
function toggleImportantUsers(displayStatus) {
  document.getElementById('importantUsers').style.display = displayStatus;
}

searchBtn.onclick = () => {
  currentTab = 'search';
  toggleImportantUsers('block'); // Zeigt wichtige Benutzer an
  usersList.innerHTML = '';
  if (searchBar.value != "") {
    searchUsers(searchBar.value);
  }
};

chatsBtn.onclick = () => {
  currentTab = 'chats';
  toggleImportantUsers('none'); // Verbirgt wichtige Benutzer
  usersList.innerHTML = '';
  loadChats();
};

function searchUsers(searchTerm) {
  let xhr = new XMLHttpRequest();
  xhr.open("POST", "php/search.php", true);
  xhr.onload = () => {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      if (xhr.status === 200) {
        let data = xhr.response;
        usersList.innerHTML = data;
      }
    }
  };
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.send("searchTerm=" + searchTerm);
}

function loadChats() {
  let xhr = new XMLHttpRequest();
  xhr.open("POST", "php/loadChats.php", true);
  xhr.onload = () => {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      if (xhr.status === 200) {
        let data = xhr.response;
        usersList.innerHTML = data;
      }
    }
  };
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.send();
}

function searchChats(searchTerm) {
  let xhr = new XMLHttpRequest();
  xhr.open("POST", "php/searchChats.php", true);
  xhr.onload = () => {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      if (xhr.status === 200) {
        let data = xhr.response;
        usersList.innerHTML = data;
      }
    }
  };
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.send("searchTerm=" + searchTerm);
}

function deleteChat(sessionId) {
  if(confirm("Are you sure you want to delete this chat?")) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "php/deleteChat.php", true);
    xhr.onload = () => {
      if(xhr.readyState === XMLHttpRequest.DONE) {
        if(xhr.status === 200) {
          let data = xhr.response;
          if(data === "success") {
            // Chat erfolgreich gelöscht, Chatliste neu laden
            loadChats();
          } else {
            alert(data); // Fehlermeldung anzeigen
          }
        }
      }
    };
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send("sessionId=" + sessionId);
  }
}
document.getElementById('profile-img').addEventListener('click', function() {
  document.getElementById('image-input').click();
});

document.getElementById('image-input').addEventListener('change', function() {
  document.getElementById('image-form').submit();
});


// Funktion, um das Webhook-Eingabefeld ein- und auszublenden
function toggleWebhookInput() {
  const webhookInput = document.querySelector('.webhook-input');
  webhookInput.style.display = webhookInput.style.display === 'none' ? 'block' : 'none';
}

// Funktion, um die Webhook-URL zu speichern
function saveWebhookUrl(userId) {
  const webhookUrl = document.getElementById('webhookUrl').value;
  if(!webhookUrl) {
      alert("Please enter a webhook URL.");
      return;
  }
  // Erstellen des XMLHttpRequest-Objekts
  const httpRequest = new XMLHttpRequest();
  if (!httpRequest) {
      alert('Cannot create an XMLHTTP instance');
      return false;
  }
  // Konfigurieren der Anfrage
  httpRequest.open("POST", "php/updateWebhook.php", true);
  httpRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

  // Festlegen der Funktion, die aufgerufen wird, wenn sich der readyState ändert
  httpRequest.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
          // Die Antwort vom Server verarbeiten
          alert(this.responseText);
          // Das Eingabefeld ausblenden, nachdem die URL erfolgreich gespeichert wurde
          document.querySelector('.webhook-input').style.display = 'none';
      }
  };
  // Senden der Anfrage mit dem Benutzer-unique_id und der Webhook-URL
  httpRequest.send("unique_id=" + userId + "&webhook=" + encodeURIComponent(webhookUrl));
}
function openWebhookModal() {
  document.getElementById('webhookModal').style.display = 'block';
  document.querySelector('.search').style.display = 'none';

}

// Funktion, um das Modal zu schließen
function closeWebhookModal() {
  document.getElementById('webhookModal').style.display = 'none';
  document.querySelector('.search').style.display = 'flex';

}
