function validateForm() {
    const message = document.getElementById('message').value.trim();
    const files = document.getElementById('attachments').files;
    const btn = document.getElementById('submitBtn');

    if (message === "" || files.length > 20) {
        btn.disabled = true;
        if (files.length > 20) {
            alert("Maksimal 20 lampiran!");
            document.getElementById('attachments').value = "";
        }
    } else {
        btn.disabled = false;
    }
}