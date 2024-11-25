document.addEventListener('DOMContentLoaded', function () {
      document.querySelectorAll('.delete').forEach(function (button) {
        button.addEventListener('click', function () {
            const notification = this.parentElement; 
            if (notification) {
                notification.remove(); 
            }
        });
    });
});
