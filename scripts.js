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
