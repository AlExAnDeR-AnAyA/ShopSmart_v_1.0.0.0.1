let cart = [];

// Cargar productos desde el servidor
async function loadProducts() {
    try {
        const response = await fetch('http://localhost/shopsmart_v_1.0.0.0.1/php/get_products.php');
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }
        const products = await response.json();
        const productList = document.getElementById('productList');

        if (products.length === 0) {
            productList.innerHTML = '<p>No se encontraron productos.</p>';
            return;
        }

        products.forEach(product => {
            const productDiv = document.createElement('div');
            productDiv.className = 'product';
            productDiv.innerHTML = `
                <img src="${product.Imagen}" alt="${product.Nombre}">
                <h2>${product.Nombre}</h2>
                <p>${product.Descripción}</p>
                <p>Precio: $${product.Precio}</p>
                <input type="number" id="quantity_${product.ID_Producto}" min="1" max="10" value="1">
                <button onclick="addToCart(${product.ID_Producto}, '${product.Nombre}', ${product.Precio})">Añadir al Carrito</button>
            `;
            productList.appendChild(productDiv);
        });
    } catch (error) {
        console.error('Error al cargar productos:', error);
    }
}

// Alternar la visualización del carrito
function toggleCart() {
    const cartContainer = document.getElementById('cart');
    cartContainer.style.display = cartContainer.style.display === 'none' || cartContainer.style.display === '' ? 'block' : 'none';
}

// Añadir producto al carrito y enviar al backend
function addToCart(productId, productName, productPrice) {
    const quantity = parseInt(document.getElementById(`quantity_${productId}`).value);
    const existingProduct = cart.find(item => item.id === productId);

    if (existingProduct) {
        existingProduct.quantity += quantity;
    } else {
        cart.push({ id: productId, name: productName, price: productPrice, quantity: quantity });
    }

    // Actualizar el carrito en el frontend
    updateCart();

    // Enviar los datos del carrito al backend
    fetch('http://localhost/shopsmart_v_1.0.0.0.1/php/add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `id_producto=${productId}&cantidad=${quantity}`,
    })
    .then(response => response.text())
    .then(data => {
        console.log(data);  // Imprime el mensaje del backend
        alert('Producto añadido al carrito');
    })
    .catch(error => {
        console.error('Error al agregar al carrito:', error);
        alert('Hubo un problema al agregar el producto al carrito. Inténtalo de nuevo.');
    });
}

// Actualizar la visualización del carrito
function updateCart() {
    const cartItemsContainer = document.getElementById('cartItems');
    cartItemsContainer.innerHTML = '';
    let total = 0;

    cart.forEach(item => {
        total += item.price * item.quantity;

        const cartItemDiv = document.createElement('div');
        cartItemDiv.className = 'cart-item';
        cartItemDiv.innerHTML = `
            <p>${item.name} (x${item.quantity})</p>
            <p>$${item.price * item.quantity}</p>
        `;

        cartItemsContainer.appendChild(cartItemDiv);
    });

    document.getElementById('cartTotal').textContent = total;
}

// Procesar el pedido
function processOrder(paymentMethod) {
    const orderDetails = {
        cart: cart,
        total: cart.reduce((sum, item) => sum + (item.price * item.quantity), 0),
        paymentMethod: paymentMethod
    };

    fetch('http://localhost/shopsmart_v_1.0.0.0.1/php/process_order.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(orderDetails)
    })
    .then(response => response.text())  // Capturar la respuesta como texto
    .then(text => {
        console.log('Response Text:', text);  // Imprimir la respuesta en la consola

        try {
            const data = JSON.parse(text);  // Intentar convertir la respuesta a JSON
            if (data.success) {
                if (paymentMethod === 'cash') {
                    alert('Pago en efectivo procesado. Puede recoger su factura en la tienda.');
                } else {
                    window.location.href = 'http://localhost/shopsmart_v_1.0.0.0.1/php/generar_factura.php?method=' + paymentMethod;
                }
            } else {
                alert('Error al procesar el pedido. Inténtelo de nuevo.');
            }
        } catch (error) {
            console.error('Error parsing JSON:', error, 'Response Text:', text);
            alert('Ocurrió un error inesperado. Por favor, intenta nuevamente más tarde.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al procesar el pedido. Inténtelo de nuevo.');
    });
}

// Proceder al pago
function proceedToPayment() {
    let paymentMethod = document.getElementById('paymentMethod').value;

    if (paymentMethod === 'cash') {
        // Si es efectivo, realiza el pago directamente
        processOrder('cash');
    } else if (paymentMethod === 'creditCard') {
        // Si es tarjeta de crédito, redirige a la página de pago con tarjeta
        window.location.href = 'http://localhost/shopsmart_v_1.0.0.0.1/php/pago_tarjeta.php';
    } else if (paymentMethod === 'transfer') {
        // Si es transferencia bancaria, redirige a la página de pago por transferencia
        window.location.href = 'http://localhost/shopsmart_v_1.0.0.0.1/php/pago_transferencia.php';
    }
}

// Al cargar la página, cargar los productos
window.onload = loadProducts;

        