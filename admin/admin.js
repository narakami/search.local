document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('addProductForm').addEventListener('submit', function(event) {
        event.preventDefault();
    
        const form = document.getElementById('addProductForm');
        const formData = new FormData(form);
    
        fetch('addProduct.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('result').textContent = data.message;
            if (data.success) {
                form.reset();
            }
        })
        .catch(error => console.error('Ошибка добавления продукта:', error));
    });

    document.getElementById('image').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const img = document.getElementById('imagePreview');
            img.src = e.target.result;
            img.style.display = 'block';
        };
    
        if (file) {
            reader.readAsDataURL(file);
        }
    });

    function fetchfood(){
        fetch('../datafood.php')
        .then(response=> {
            if(!response.ok){
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(data => displayfoodbd(data))
        .catch(error => console.error('There was a problem with the fetch operation:', error));
    }
    
    function displayfoodbd(data){
        const prodlist = document.getElementById('result');
        prodlist.innerHTML = '';
        data.forEach(item => {
            const li = document.createElement('li');
            li.innerHTML = `<form enctype="multipart/form-data" class="froman${item.id}" id="${item.id}">
                <span class="image"><img src="picture/${item.image}" alt="" class="imagh"></span><br>
                Продукт: <span class="namefood">${item.namefood}</span><br>
                Тег: <span class="tag">${item.tag}</span><br>
                Кол-во: <span class="count">${item.count}</span><br>
                Цена: <span class="price">${item.price}</span><br>
                <button type="button" class="editbutton" data-id="${item.id}">Изменить</button>
                <button type="button" class="deleteButton" data-id="${item.id}">Удалить</button>
            </form>`;
            prodlist.appendChild(li);
        });

        document.querySelectorAll('.deleteButton').forEach(button => {
            button.addEventListener('click', deleteProduct);
        });

        document.querySelectorAll('.editbutton').forEach(button => {
            button.addEventListener('click', handleEditProduct);
        });
    }

    function handleEditProduct(event) {
        const button = event.target;
        const productId = button.getAttribute('data-id');
        const form = document.getElementById(productId);
        
        const nameSpan = form.querySelector('.namefood');
        const tagSpan = form.querySelector('.tag');
        const countSpan = form.querySelector('.count');
        const priceSpan = form.querySelector('.price');
        
        // Создаем поле для ввода изображения
        // Вставляем поле выбора изображения в div с изображением
        
        // Редактируем остальные данные
        nameSpan.innerHTML = `<input type="text" id="name" name="name" value="${nameSpan.textContent.trim()}">`;
        tagSpan.innerHTML = `<input type="text" id="tag" name="tag" value="${tagSpan.textContent.trim()}">`;
        countSpan.innerHTML = `<input type="number" id="count" name="count" value="${countSpan.textContent.trim()}">`;
        priceSpan.innerHTML = `<input type="number" id="price" name="price" value="${priceSpan.textContent.trim()}"><br>
        ФОТО <input type="file" id="image" name="image" accept="image/*">
        `;
        
        button.textContent = 'Сохранить';
        button.removeEventListener('click', handleEditProduct);
        button.addEventListener('click', handleSaveProduct);
    }
    

    function handleSaveProduct(event) {
        event.preventDefault();
        const button = event.target;
        const productId = button.getAttribute('data-id');
        const form = document.querySelector(`.froman${productId}`);
        
        const formData = new FormData(form);
        formData.append('id', productId);
        for (let [key, value] of formData.entries()) {
            console.log(key, value);
        }
        
    
        fetch('changeproduct.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log(data);
            fetchfood(); // Перезагружаем данные с сервера
        })
        .catch(error => console.error(error));
    }
    

    function deleteProduct(event) {
        const productId = event.target.getAttribute('data-id');
        fetch('datafood.php', {
            method: 'DELETE',
            headers: { 'Content-type': 'application/json' },
            body: JSON.stringify({ id: productId })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            console.log(data);
            fetchfood();
        })
        .catch(error => console.error('There was a problem with the fetch operation:', error));
    };

    fetchfood();
});
