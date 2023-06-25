function changeText(button, text, textToChangeBackTo) {
  buttonId = document.getElementById(button);
  buttonId.textContent = text;
  setTimeout(function() { document.getElementById(button).textContent = textToChangeBackTo; }, 5000);
}

// Get the modal
var modal = document.getElementById('id01');

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
if (event.target == modal) {
    modal.style.display = "none";
}
}

function sendData(fonction, ID) {
  var xhr = new XMLHttpRequest();
  xhr.open("POST", "http://192.168.1.4", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.send("fonction=" + encodeURIComponent(fonction) + "&ID=" + encodeURIComponent(ID));
}