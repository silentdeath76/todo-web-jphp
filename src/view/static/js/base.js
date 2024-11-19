let cardId = -1;

document.addEventListener('DOMContentLoaded', () => {
    makeCardList()

    document.getElementById('rollback').addEventListener("click", function () {
        document.getElementById('container').innerHTML = null;
        makeCardList()

        document.getElementById('rollback').style.visibility = 'hidden';
        document.getElementById('makeTask').style.display = 'none';
    });
});


function makeAjaxRequest(url, method, data, callback) {
    const xhr = new XMLHttpRequest();
    xhr.open(method, url, true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.onload = function () {
        if (xhr.status === 200) {
            callback(JSON.parse(xhr.responseText));
        } else {
            console.error('Error:', xhr.statusText);
        }
    };
    xhr.onerror = function () {
        console.error('Error:', xhr.statusText);
    };
    xhr.send(JSON.stringify(data));
}

function makeRow(id, title, done, cardId) {
    const row = document.createElement('div');
    row.className = 'row';

    const checkbox = document.createElement('input');
    checkbox.addEventListener('change', () => {
        if (checkbox.checked) {
            label.classList.add('checked');
        } else {
            label.classList.remove('checked');
        }

        makeAjaxRequest('/todos/' + id, 'PUT',
            JSON.stringify({
                "title": title,
                "done": checkbox.checked,
                "cardId": cardId
            }), function (status) {
                if (status.status !== "ok") {
                    console.error("error update element")
                }
            }
        );
    })

    checkbox.type = 'checkbox';
    checkbox.name = id;
    checkbox.id = id;
    checkbox.checked = done;

    const label = document.createElement('label');
    label.className = 'title';
    label.htmlFor = id;
    label.id = 'label-' + id;
    label.style.userSelect = 'none';
    label.textContent = title;

    if (done === true) {
        label.className = 'checked';
    }

    row.appendChild(checkbox);
    row.appendChild(label);


    return row;
}

function makeCard(cardData) {
    // Создаем контейнер для карточки
    const card = document.createElement('div');
    card.classList.add('card');
    card.classList.add('outline');
    card.classList.add('contrast');

    // Создаем заголовок карточки
    const title = document.createElement('div');
    title.classList.add('title');
    title.textContent = cardData.title;

    // Создаем детали карточки
    const details = document.createElement('div');
    details.classList.add('details');
    details.textContent = cardData.details;

    // Добавляем заголовок и детали в карточку
    card.appendChild(title);
    card.appendChild(details);

    card.addEventListener("click", function () {
        cardId = cardData.id
        makeRowList(cardData.id);

        document.getElementById('makeTask').style.display = 'block';
        document.getElementById('makeCard').style.display = 'none';
    })

    return card
}

function makeCardList() {
    makeAjaxRequest('/cards', 'GET', null, (response) => {
        let container = document.getElementById('container');

        container.innerHTML = "";

        for (let card in response) {
            const cardObject = response[card];
            const cardElement = makeCard(cardObject);
            container.appendChild(cardElement)
        }
        document.getElementById('makeTask').style.display = 'none';
        document.getElementById('makeCard').style.display = 'block';

    })
}

function makeRowList(id) {
    makeAjaxRequest('/todos/card/' + id, "GET", null, function (response) {
        document.getElementById('container').innerHTML = null;
        document.getElementById('rollback').style.visibility = 'visible';


        for (var row in response) {
            const todo = response[row];
            const rowElement = makeRow(todo.id, todo.title, todo.done, todo.cardId);
            document.getElementById('container').appendChild(rowElement);
        }
    })
}

function createCard() {
    let root = document.getElementById("card-create"),
        title = root.getElementsByTagName("input").item(0).value,
        details = root.getElementsByTagName("textarea").item(0).value;

    console.log(title, details)
    makeAjaxRequest("/cards", "POST", JSON.stringify({
        "title": title,
        "details": details
    }), function (params) {
        document.getElementById('container').appendChild(makeCard(params))
        closeModal(root)
    })
}

function createTask() {
    let task = document.getElementById("task-create").getElementsByTagName("input").item(0).value

    makeAjaxRequest("/todos", "POST", JSON.stringify({
        "id": cardId,
        "title": task
    }), function (params) {
        console.log(params)
        document.getElementById('container').appendChild(makeRow(params.id, params.title, params.done, params.cardId))
        closeModal(document.getElementById("task-create"))
    })
}

// import * as view_static_js_modal from "view/static/js/modal";


class API {
    cardId = -1;


    constructor() {

    }

    make () {
        
    }
}