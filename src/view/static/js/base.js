
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
        xhr.withCredentials = true;              // ← на всякий случай
        xhr.onload = function () {
            if (xhr.status >= 200 && xhr.status < 300) {
                let data = null;
                try { data = JSON.parse(xhr.responseText); } catch (e) {}
                callback(data);
            } else {
                console.error('Error:', xhr.status, xhr.responseText);
            }
        };
        xhr.onerror = function () {
            console.error('Error:', xhr.statusText);
        };
        // this.data уже объект — один раз stringify
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
            const response = await fetch('/cards', {credentials: 'same-origin'});
            const cards = await response.json();
            this.renderCardList(cards);
        } catch (error) {
            console.error('Error loading cards:', error);
        }
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

        // Цвет иконки — детерминированный по id/названию
        const colors = ['#f97316', '#e11d48', '#22c55e', '#3b82f6', '#8b5cf6', '#ec4899', '#06b6d4', '#eab308'];
        const colorIdx = (typeof card.id === 'number' ? card.id : card.title.length) % colors.length;
        const color = colors[colorIdx];
        const initial = (card.title || '?').trim().charAt(0).toUpperCase();

        cardElement.innerHTML = `
        <div class="card-header">
            <div class="card-icon" style="background:${color}">${initial}</div>
            <div class="card-title-block">
                <h3 class="card-title">${escapeHtml(card.title)}</h3>
                <span class="card-category">Карточка</span>
            </div>
        </div>
        <p class="card-details">${escapeHtml(card.details || '')}</p>
        <div class="card-footer">
            <span class="card-category">ID: ${card.id}</span>
            <button class="open-btn" type="button">
                Открыть
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </button>
        </div>
    `;

        cardElement.addEventListener('click', (e) => {
            // Клик по «Открыть» и по карточке — одно и то же
            this.cardId = card.id;
            this.makeRowList(card.id);
            this.makeTaskButton.style.display = 'block';
            this.makeCardButton.style.display = 'none';
        });

        return cardElement;
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
        const title   = document.getElementById('card-title').value.trim();
        const details = document.getElementById('card-details').value.trim();
        if (!title) { alert('Введите заголовок'); return; }

        new AjaxRequest('/cards', 'POST', {title, details}).send((card) => {
            this.container.appendChild(this.makeCard(card));
            this.closeModal(document.getElementById('card-create'));
        });
    }

    createTask() {
        if (!this.cardId || this.cardId === -1) { alert('Сначала откройте карточку'); return; }
        const title = document.getElementById('task-title').value.trim();
        if (!title) { alert('Введите название задачи'); return; }

        new AjaxRequest('/tasks', 'POST', {title, done: false, cardId: this.cardId}).send((task) => {
            this.container.appendChild(this.makeRow(task));
            this.closeModal(document.getElementById('task-create'));
        });
    }

    closeModal(root) {
        root.style.display = 'none';
    }
}

async function signin() {
    const username = document.getElementById('signin-login').value;
    const password = document.getElementById('signin-password').value;

    const r = await fetch('/account/login', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        credentials: 'same-origin',
        body: JSON.stringify({username, password})
    });
    const data = await r.json();

    if (data.status === 'ok') {
        closeModal(document.getElementById('signin'));
        location.reload();
    } else {
        alert(data.message || 'Ошибка входа');
    }
}

async function signup() {
    const username = document.getElementById('signup-login').value;
    const password = document.getElementById('signup-password').value;
    const repeat   = document.getElementById('signup-password-repeat').value;

    if (password !== repeat) { alert('Пароли не совпадают'); return; }

    const r = await fetch('/account/create', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        credentials: 'same-origin',
        body: JSON.stringify({username, password, email: ''})
    });
    const data = await r.json();

    if (data.status !== 'ok') {
        alert(data.message || 'Ошибка регистрации');
        return;
    }

    // авто-логин
    const r2 = await fetch('/account/login', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        credentials: 'same-origin',
        body: JSON.stringify({username, password})
    });
    const d2 = await r2.json();

    if (d2.status === 'ok') {
        location.reload();
    } else {
        alert(d2.message || 'Ошибка входа');
    }
}

async function logout() {
    await fetch('/account/logout', {method: 'POST', credentials: 'same-origin'});
    location.reload();
}

async function me() {
    const r = await fetch('/account/me');
    return r.ok ? (await r.json()).user : null;
}

function escapeHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

/* ============ Тема ============ */
function applyTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
    localStorage.setItem('theme', theme);
}

function toggleTheme() {
    const current = document.documentElement.getAttribute('data-theme') || 'dark';
    applyTheme(current === 'dark' ? 'light' : 'dark');
}

window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
    if (!localStorage.getItem('theme')) {
        document.documentElement.setAttribute('data-theme', e.matches ? 'dark' : 'light');
    }
});

document.addEventListener('mousemove', (e) => {
    const card = e.target.closest('.card');
    if (!card) return;
    const rect = card.getBoundingClientRect();
    card.style.setProperty('--mouse-x', `${e.clientX - rect.left}px`);
    card.style.setProperty('--mouse-y', `${e.clientY - rect.top}px`);
});

const cardManager = new CardManager();
cardManager.loadCards();
