document.addEventListener('DOMContentLoaded',() => {
    document.getElementById('uploadForm').addEventListener('submit', function(event) {
        event.preventDefault();
    
        const form = document.getElementById('uploadForm');
        const formData = new FormData(form);
    
        fetch('upload.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                getphoto()
                form.reset();
            }
        })
        .catch(error => console.error('Ошибка добавления продукта:', error));
    });
    function getphoto(){
        fetch('get_photos.php')
        .then(response=> {
            if(!response.ok){
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(data => displayphoto(data))
        .catch(error => console.error('There was a problem with the fetch operation:', error));
    }
    function displayphoto(data){
        const photolist = document.getElementById('result');
        photolist.innerHTML = '';
        data.forEach(item => {
            const form = document.createElement('form');
            form.innerHTML=`
                <form id="updateform" enctype="multipart/form-data">
                <input type="hidden" name="id" value="${item.id}">
                <img src="uploads/${item.image}" alt="" width="200px" height="200px">
                <label for="image">Выберите фото:</label>
                <input type="file" id="image" name="image" accept="image/*" required><br><br>
                <button type="submit">Загрузить</button>
                </form>
                
            `;
            
        photolist.appendChild(form);
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(form);
            fetch('update_photo.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    getphoto()
                }
            })
            .catch(error => console.error('Ошибка обновления фото:', error));
            })
        });
    }
    getphoto();
})