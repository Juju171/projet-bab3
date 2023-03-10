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

// function auth(event) { //Modifier cette fonction pour quelle vérifie si les données sont bonnes à l'aide de la base de donnée 
//           event.preventDefault();

//           var ID = document.getElementById("ID").value;
//           var password = document.getElementById("password").value;

//           if (ID === "1" && password === "1234") {
//                window.location.replace("site.html");
//           } else {
//               alert("Invalid information");
//               return;
//             }
//     }