let fileInputs = document.querySelectorAll('input[type="file"]');
let imageNames = document.querySelectorAll('.image_name');

console.log(fileInputs);
console.log(imageNames);

fileInputs.forEach((input) => {
    input.addEventListener('change', () => {
        console.log('change');

        let fileName = input.files[0].name;
        let fileTmpUrl = URL.createObjectURL(input.files[0]);
        let key = input.getAttribute('data-key');

        let image = document.querySelector(`img[data-key="${key}"]`);
        image.setAttribute('src', fileTmpUrl);

        let imageName = document.querySelector(`.image_name[data-key="${key}"]`);
        imageName.textContent = fileName;
    });
});

// Affichage popup confirmation
// Recuperation des arguments GET

let params = (new URL(document.location)).searchParams;
popupId = 'confirmPopup';
//action is set
if(params.get('action')){
    switch(params.get('action')){
        case 'delete':
            document.getElementById(popupId).classList.add('active', 'delete');
            break;
        case 'add':
            document.getElementById(popupId).classList.add('active', 'add');
            break;
        case 'update':
            document.getElementById(popupId).classList.add('active', 'update');
            break;
        case 'error':
            document.getElementById(popupId).classList.add('active', 'error');
            break;
    }

    // Disparition popup
    setTimeout(() => {
        document.getElementById(popupId).classList.remove('active');
    }, 3000);
}