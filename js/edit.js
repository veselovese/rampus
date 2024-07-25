function uploadAvatar() {
    $('#user-avatar').trigger('click');
}

const userAvatar = document.getElementById('user-avatar');
userAvatar.addEventListener('change', (e) => {
    const file = e.target.files;

    if (file.length > 0) {
        const [currentFile] = file;
        const fileType = currentFile.type.startsWith('image');
        const reader = new FileReader();
        if (fileType) {
            reader.onload = () => {
                document.getElementById('current-avatar').src = reader.result;
            }
        }
        reader.readAsDataURL(currentFile);
    }
})