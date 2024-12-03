
class AjaxRequest {
    constructor(url, method, data) {
        this.url = url;
        this.method = method;
        this.data = data;
    }

    send(callback) {
        const xhr = new XMLHttpRequest();
        xhr.open(this.method, this.url, true);
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
        xhr.send(JSON.stringify(this.data));
    }
}

class CardManager {
    constructor() {
        this.cardId = -1;
        this.container = document.getElementById('container');
        this.rollbackButton = document.getElementById('rollback');
        this.makeTaskButton = document.getElementById('makeTask');
        this.makeCardButton = document.getElementById('makeCard');

        this.rollbackButton.addEventListener("click", () => this.rollback());
        this.makeTaskButton.style.display = 'none';
    }

    async loadCards() {
        try {
            const response = await fetch('/cards');
            const cards = await response.json();
            this.renderCardList(cards);
        } catch (error) {
            console.error('Error loading cards:', error);
        }
    }

    renderCardList(cards) {
        this.container.innerHTML = '';
        for (let card of cards) {
            const cardElement = this.makeCard(card);
            this.container.appendChild(cardElement);
        }
        this.makeTaskButton.style.display = 'none';
        this.makeCardButton.style.display = 'block';
    }

    makeCard(card) {
        const cardElement = document.createElement('div');
        cardElement.classList.add('card');

        cardElement.innerHTML = `
            <h2>${card.title}</h2>
            <p>${card.details}</p>
        `;

        cardElement.addEventListener('click', () => {
            this.cardId = card.id;
            this.makeRowList(card.id);
            this.makeTaskButton.style.display = 'block';
            this.makeCardButton.style.display = 'none';
        });

        return cardElement;
    }

    async makeRowList(id) {
        try {
            const response = await fetch(`/tasks/card/${id}`);
            const tasks = await response.json();
            this.container.innerHTML = '';
            this.rollbackButton.style.visibility = 'visible';

            for (let task of tasks) {
                const rowElement = this.makeRow(task);
                this.container.appendChild(rowElement);
            }
        } catch (error) {
            console.error('Error loading tasks:', error);
        }
    }

    makeRow(todo) {
        const rowElement = document.createElement('div');
        rowElement.classList.add('task');

        rowElement.innerHTML = `
        <label>
            <input type="checkbox" ${todo.done ? 'checked' : ''}>
            ${todo.title}
        </label>
    `;
        rowElement.addEventListener('click', function (event) {
            if (event.target.tagName === 'INPUT') {
                new AjaxRequest('/tasks/' + todo.id, "PUT", {
                    "title": todo.title,
                    "done": event.target.checked,
                    "cardId": todo.cardId
                }).send(function (params) {
                    if (params.status !== "ok") {
                        console.error(params);
                    }
                });
            }
        });

        return rowElement;
    }

    rollback() {
        this.container.innerHTML = '';
        this.rollbackButton.style.visibility = 'hidden';
        this.makeTaskButton.style.display = 'none';
        this.makeCardButton.style.display = 'block';
        this.loadCards();
    }

    createCard() {
        const title = document.getElementById("card-create").getElementsByTagName("input").item(0).value;
        const details = document.getElementById("card-create").getElementsByTagName("textarea").item(0).value;

        new AjaxRequest("/cards", "POST", JSON.stringify({
            "title": title,
            "details": details
        })).send((params) => {
            this.container.appendChild(this.makeCard(params));
            this.closeModal(document.getElementById("card-create"));
        });
    }

    createTask() {
        const task = document.getElementById("task-create").getElementsByTagName("input").item(0).value;

        new AjaxRequest("/tasks", "POST", JSON.stringify({
            "id": this.cardId,
            "title": task
        })).send((params) => {
            this.container.appendChild(this.makeRow(params));
            this.closeModal(document.getElementById("task-create"));
        });
    }

    closeModal(root) {
        root.style.display = 'none';
    }
}

const cardManager = new CardManager();
cardManager.loadCards();
