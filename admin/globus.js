document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('auth').addEventListener('click', () => {
        document.getElementById('authform').classList.toggle('authform');
    });
    document.getElementById('regia').addEventListener('click', () => {
        document.getElementById('dauth').classList.toggle('none');
    });

    function authf() {
        var login = document.getElementById('login').value;
        var password = document.getElementById('password').value;

        fetch('../useraccaunt.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ login, password })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            // Обрабатываем результат авторизации
            if (data.success) { // Предполагаем, что сервер возвращает поле 'success'
                console.log('Успешная авторизация:', data.username);
                loadSessionInfo(); // Перезагрузка информации о сессии
            } else {
                console.error('Ошибка авторизации:', data.message);
                alert(data.message || 'Не удалось авторизоваться');
            }
        })
        .catch(error => console.error('Ошибка аутентификации:', error));
    }

    // Функция для загрузки информации о сессии
    function loadSessionInfo() {
        fetch('../session_info.php')
        .then(response => response.json())
        .then(data => {
            if (data.isLoggedIn) {
                console.log('Пользователь авторизован:', data.username);
                const logineg = document.getElementById('authin');
                logineg.innerHTML = '';
                logineg.innerHTML = `<a href='#' id="vsenorm">${data.username}</a>
                <div class="none" id="logout"><button id="logoutButton">выход</button></div>`;

                // Установка обработчика событий для кнопки выхода
                document.getElementById('vsenorm').addEventListener('click',()=>{
                    document.getElementById('logout').classList.toggle('none')
                })
                document.getElementById('logoutButton').addEventListener('click', () => {
                    fetch('../logout.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'logged_out') {
                            logineg.innerHTML = '';
                            logineg.innerHTML = `<a href="#" id="auth">Войти</a>
                            <form action="" method="POST" class="authform tauth" id="authform">
                            <input name="login" id="login" required placeholder="login">
                            <br>
                            <input name="password" id="password" required placeholder="password">
                            <br>
                            <input type="submit" name="auth" id="auth">
                            </form>`;
                        }
                        location.reload()
                    })
                    .catch(error => console.error('Ошибка выхода:', error));
                });
            } else {
                console.log('Пользователь не авторизован');
            }
        })
        .catch(error => console.error('Ошибка при получении сессии:', error));
    }
    let products = []; // Объявляем переменную для хранения списка продуктов

    function fetchfood() {
        fetch('../datafood.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                products = data; // Записываем данные в переменную products
                console.log('Список продуктов загружен:', products); // Логируем загруженные продукты
                tovar(data)
            })
            .catch(error => {
                console.error('There was a problem with the fetch operation:', error);
            });
    }
    
    document.getElementById('namefood').addEventListener('input', (event) => {
        const query = event.target.value; // Получаем значение из поля ввода
        filterProducts(query); // Фильтруем продукты на основе запроса
        document.getElementById('secretsearch').classList.remove('none')
    });
    document.addEventListener('click', (event) => {
        const input = document.getElementById('namefood');
        const resultsContainer = document.getElementById('secretsearch');
    
        // Проверяем, если клик произошел за пределами input и resultsContainer
        if (!input.contains(event.target) && !resultsContainer.contains(event.target)) {
            resultsContainer.classList.add('none'); // Скрываем результаты
        }
    });
    function filterProducts(query) {
        const filteredProducts = products.filter(product => {
            const name = product.namefood ? product.namefood.trim() : ''; // Удаляем пробелы
            const tag = product.tag ? product.tag.trim() : ''; // Удаляем пробелы
    
            return name.toLowerCase().includes(query.toLowerCase()) ||
                   tag.toLowerCase().includes(query.toLowerCase());
        });
    
        displayFilteredProducts(filteredProducts); // Отображение отфильтрованных продуктов
    }
    
    // Функция для отображения отфильтрованных продуктов
    function displayFilteredProducts(filteredProducts) {
        const resultsDiv = document.getElementById('results');
        resultsDiv.innerHTML = ''; // Очищаем предыдущие результаты
        if (filteredProducts.length > 0) {
            filteredProducts.forEach(product => {
                const productDiv = document.createElement('div');
                productDiv.textContent = `${product.namefood} - ${product.tag}`;
                resultsDiv.appendChild(productDiv);
            });
        } else {
            resultsDiv.textContent = 'Товары не найдены';
        }
    }
    document.getElementById('register').addEventListener('submit',function(event){
        event.preventDefault();
        var login = document.getElementById('rlogin').value;
        var password = document.getElementById('rpassword').value;
        var spassword = document.getElementById('rsecondpassword').value;
        if (password !== spassword) {
            alert('Пароли не совпадают!');
            return;
        }
        
            fetch('../reg.php',{
                method:'POST',
            headers: {
                'Content-Type':'application/json'
            },
            body: JSON.stringify({login,password})
            })
            .then(response=>{
                if(!response.ok){
                    throw new Error('Network response was not ok ' + response.statusText);
                }
                return response.json();
            })
            .then(data=>{
                console.log(data.message); // Показываем сообщение
                alert(data.message);
    
            // Проверка на успешную регистрацию
            if (data.status === 'success') {
                location.reload();
            }
            })
            .catch(error=>{
                console.error('There was a problem with the fetch operation:', error);
            })
            
        
    })
    function tovar(data) {
    const tvr = document.getElementById('categories');
    tvr.innerHTML = "";  // Очищаем текущие данные

    // Ограничиваем количество товаров до 6, если их больше
    const limitedData = data.slice(0, 6);

    // Отображаем товары
    limitedData.forEach(item => {
        const li = document.createElement('div');
        li.innerHTML = `
            <div class="product-card">
                <div class="product-image">
                    <img src="picture/${item.image}" alt="Product Image">
                </div>
                <div class="product-details">
                    <h2 class="product-name">${item.namefood}</h2>
                    <p class="product-tag">Тег: <span>${item.tag}</span></p>
                    <p class="product-count">Количество: <span>${item.count}</span></p>
                    <p class="product-price">Цена: <span>${item.price}</span></p>
                    <button class="add-to-cart">Добавить в корзину</button>
                </div>
            </div>
        `;
        tvr.appendChild(li);
    });
}
    // Установка обработчика события для формы авторизации
    document.getElementById('authform').addEventListener('submit', authf);

    // Инициализация загрузки информации о сессии при загрузке страницы
    fetchfood();
    loadSessionInfo();
    
});
