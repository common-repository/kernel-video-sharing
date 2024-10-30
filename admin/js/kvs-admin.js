const span2copy = document.querySelector("span.copy2cb");
if ( span2copy != null ) {
    span2copy.onclick = function() {
      document.execCommand("copy");
    }
    span2copy.addEventListener("copy", function(event) {
      event.preventDefault();
      if (event.clipboardData) {
        event.clipboardData.setData("text/plain", span2copy.textContent);
      }
    });
}