<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Mobile Todo App</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .dragging {
            opacity: 0.5;
        }
        /* Mobile first styles */
        @media (min-width: 768px) {
            #sidebar {
                display: flex !important;
            }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center p-4">
    <div class="flex w-full max-w-md bg-white rounded-lg shadow p-4 flex-col min-h-screen">
        <!-- Header -->
        <header class="mb-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">Today's Tasks</h1>
            <button id="open-task-form-btn" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Add Task</button>
        </header>

        <!-- Search -->
        <input type="text" id="search-input" placeholder="Search tasks..." class="mb-4 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" />

        <!-- Task Lists -->
        <div id="task-lists" class="flex flex-col space-y-6 overflow-auto flex-grow">
            <section>
                <h2 class="text-lg font-semibold mb-2">Pending Tasks</h2>
                <div id="pending-tasks" class="space-y-2"></div>
            </section>
            <section>
                <h2 class="text-lg font-semibold mb-2">Completed Tasks</h2>
                <div id="completed-tasks" class="space-y-2"></div>
            </section>
        </div>

        <!-- Task Form Modal -->
        <div id="task-form-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
            <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md relative">
                <button id="close-task-form-btn" class="absolute top-3 right-3 text-gray-600 hover:text-gray-900 text-xl font-bold">&times;</button>
                <form id="task-form" class="space-y-4">
                    <input type="hidden" id="task-id" />
                    <div>
                        <label for="title" class="block text-gray-700 font-semibold mb-1">Title <span class="text-red-500">*</span></label>
                        <input type="text" id="title" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required />
                    </div>
                    <div>
                        <label for="description" class="block text-gray-700 font-semibold mb-1">Description</label>
                        <textarea id="description" rows="3" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                    <div>
                        <label for="start_date" class="block text-gray-700 font-semibold mb-1">Start Date</label>
                        <input type="date" id="start_date" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label for="due_date" class="block text-gray-700 font-semibold mb-1">Due Date</label>
                        <input type="date" id="due_date" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label for="priority" class="block text-gray-700 font-semibold mb-1">Priority</label>
                        <select id="priority" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="Low">Low</option>
                            <option value="Medium" selected>Medium</option>
                            <option value="High">High</option>
                        </select>
                    </div>
                    <div>
                        <label for="category" class="block text-gray-700 font-semibold mb-1">Category</label>
                        <select id="category" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"></select>
                        <input type="text" id="new-category" placeholder="New category" class="mt-2 w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        <button type="button" id="add-category-btn" class="mt-2 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition w-full">Add Category</button>
                    </div>
                    <div>
                        <label for="repetition" class="block text-gray-700 font-semibold mb-1">Repetition</label>
                        <select id="repetition" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="None" selected>None</option>
                            <option value="Daily">Daily</option>
                            <option value="Weekly">Weekly</option>
                            <option value="Monthly">Monthly</option>
                            <option value="Yearly">Yearly</option>
                        </select>
                    </div>
                    <div id="repetition-details" class="mt-2 hidden">
                        <div id="weekly-options" class="hidden">
                            <label class="block text-gray-700 font-semibold mb-1">Select Days of Week</label>
                            <div class="flex space-x-2">
                                <label><input type="checkbox" value="Mon" class="weekday-checkbox" /> Mon</label>
                                <label><input type="checkbox" value="Tue" class="weekday-checkbox" /> Tue</label>
                                <label><input type="checkbox" value="Wed" class="weekday-checkbox" /> Wed</label>
                                <label><input type="checkbox" value="Thu" class="weekday-checkbox" /> Thu</label>
                                <label><input type="checkbox" value="Fri" class="weekday-checkbox" /> Fri</label>
                                <label><input type="checkbox" value="Sat" class="weekday-checkbox" /> Sat</label>
                                <label><input type="checkbox" value="Sun" class="weekday-checkbox" /> Sun</label>
                            </div>
                        </div>
                        <div id="monthly-options" class="hidden">
                            <label for="monthly-day" class="block text-gray-700 font-semibold mb-1">Day of Month (1-31)</label>
                            <input type="number" id="monthly-day" min="1" max="31" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        </div>
                        <div id="yearly-options" class="hidden">
                            <label for="yearly-month" class="block text-gray-700 font-semibold mb-1">Month</label>
                            <select id="yearly-month" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="1">January</option>
                                <option value="2">February</option>
                                <option value="3">March</option>
                                <option value="4">April</option>
                                <option value="5">May</option>
                                <option value="6">June</option>
                                <option value="7">July</option>
                                <option value="8">August</option>
                                <option value="9">September</option>
                                <option value="10">October</option>
                                <option value="11">November</option>
                                <option value="12">December</option>
                            </select>
                            <label for="yearly-day" class="block text-gray-700 font-semibold mb-1 mt-2">Day</label>
                            <input type="number" id="yearly-day" min="1" max="31" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        </div>
                    </div>
                    <div class="flex justify-end mt-4">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">Save Task</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script>
const taskForm = document.getElementById('task-form');
const tasksListPending = document.getElementById('pending-tasks');
const tasksListCompleted = document.getElementById('completed-tasks');
const taskIdInput = document.getElementById('task-id');
const titleInput = document.getElementById('title');
const descriptionInput = document.getElementById('description');
const startDateInput = document.getElementById('start_date');
const dueDateInput = document.getElementById('due_date');
const priorityInput = document.getElementById('priority');
const categorySelect = document.getElementById('category');
const newCategoryInput = document.getElementById('new-category');
const addCategoryBtn = document.getElementById('add-category-btn');
const repetitionSelect = document.getElementById('repetition');
const repetitionDetailsDiv = document.getElementById('repetition-details');
const weeklyOptions = document.getElementById('weekly-options');
const monthlyOptions = document.getElementById('monthly-options');
const yearlyOptions = document.getElementById('yearly-options');
const searchInput = document.getElementById('search-input');
const openTaskFormBtn = document.getElementById('open-task-form-btn');
const closeTaskFormBtn = document.getElementById('close-task-form-btn');
const taskFormModal = document.getElementById('task-form-modal');

let allTasks = [];
let allCategories = [];

function formatDate(dateStr) {
    if (!dateStr) return '';
    const d = new Date(dateStr);
    return d.toLocaleDateString();
}

function getTodayStr() {
    return new Date().toISOString().split('T')[0];
}

function getRepetitionDetails() {
    const val = repetitionSelect.value;
    if (val === 'Weekly') {
        const checkedDays = Array.from(document.querySelectorAll('.weekday-checkbox:checked')).map(cb => cb.value);
        return JSON.stringify({ days: checkedDays });
    } else if (val === 'Monthly') {
        const day = document.getElementById('monthly-day').value;
        return JSON.stringify({ day: day });
    } else if (val === 'Yearly') {
        const month = document.getElementById('yearly-month').value;
        const day = document.getElementById('yearly-day').value;
        return JSON.stringify({ month: month, day: day });
    }
    return null;
}

function setRepetitionDetails(details) {
    if (!details) return;
    try {
        const obj = JSON.parse(details);
        if (obj.days) {
            weeklyOptions.classList.remove('hidden');
            repetitionDetailsDiv.classList.remove('hidden');
            document.querySelectorAll('.weekday-checkbox').forEach(cb => {
                cb.checked = obj.days.includes(cb.value);
            });
        } else if (obj.day) {
            monthlyOptions.classList.remove('hidden');
            repetitionDetailsDiv.classList.remove('hidden');
            monthlyOptions.querySelector('input').value = obj.day;
        } else if (obj.month && obj.day) {
            yearlyOptions.classList.remove('hidden');
            repetitionDetailsDiv.classList.remove('hidden');
            yearlyOptions.querySelector('select').value = obj.month;
            yearlyOptions.querySelector('input').value = obj.day;
        }
    } catch (e) {
        // ignore
    }
}

repetitionSelect.addEventListener('change', () => {
    repetitionDetailsDiv.classList.add('hidden');
    weeklyOptions.classList.add('hidden');
    monthlyOptions.classList.add('hidden');
    yearlyOptions.classList.add('hidden');

    const val = repetitionSelect.value;
    if (val === 'Weekly') {
        repetitionDetailsDiv.classList.remove('hidden');
        weeklyOptions.classList.remove('hidden');
    } else if (val === 'Monthly') {
        repetitionDetailsDiv.classList.remove('hidden');
        monthlyOptions.classList.remove('hidden');
    } else if (val === 'Yearly') {
        repetitionDetailsDiv.classList.remove('hidden');
        yearlyOptions.classList.remove('hidden');
    }
});

async function fetchCategories() {
    const res = await fetch('categories.php');
    if (!res.ok) return;
    allCategories = await res.json();
    renderCategories();
    populateCategorySelect();
}

function renderCategories() {
    const categoryList = document.getElementById('category-list');
    if (!categoryList) return;
    categoryList.innerHTML = '';
    const allCatLi = document.createElement('li');
    const allCatBtn = document.createElement('button');
    allCatBtn.textContent = 'All Categories';
    allCatBtn.className = 'w-full text-left px-3 py-2 rounded hover:bg-gray-100 transition text-gray-700 font-semibold';
    allCatBtn.addEventListener('click', () => {
        currentCategoryId = null;
        filterAndRenderTasks();
    });
    allCatLi.appendChild(allCatBtn);
    categoryList.appendChild(allCatLi);

    allCategories.forEach(cat => {
        const li = document.createElement('li');
        const btn = document.createElement('button');
        btn.textContent = cat.name;
        btn.className = 'w-full text-left px-3 py-2 rounded hover:bg-gray-100 transition text-gray-700';
        btn.addEventListener('click', () => {
            currentCategoryId = cat.id;
            filterAndRenderTasks();
        });
        li.appendChild(btn);
        categoryList.appendChild(li);
    });
}

function populateCategorySelect() {
    categorySelect.innerHTML = '<option value="">None</option>';
    allCategories.forEach(cat => {
        const option = document.createElement('option');
        option.value = cat.id;
        option.textContent = cat.name;
        categorySelect.appendChild(option);
    });
}

addCategoryBtn.addEventListener('click', async () => {
    const name = newCategoryInput.value.trim();
    if (!name) return;
    const res = await fetch('categories.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name })
    });
    if (res.ok) {
        newCategoryInput.value = '';
        await fetchCategories();
    } else {
        alert('Failed to add category');
    }
});

async function fetchTasks() {
    const res = await fetch('tasks.php');
    if (!res.ok) return;
    allTasks = await res.json();
    filterAndRenderTasks();
}

let currentFilter = 'today';
let currentCategoryId = null;

function filterAndRenderTasks() {
    let filtered = allTasks;

    if (currentCategoryId) {
        filtered = filtered.filter(task => task.category_id === currentCategoryId);
    }

    const todayStr = getTodayStr();

    switch (currentFilter) {
        case 'today':
            filtered = filtered.filter(task => task.due_date === todayStr);
            break;
        case 'pending':
            filtered = filtered.filter(task => task.status === 'Pending');
            break;
        case 'completed':
            filtered = filtered.filter(task => task.status === 'Completed');
            break;
        case 'overdue':
            filtered = filtered.filter(task => task.due_date && task.due_date < todayStr && task.status === 'Pending');
            break;
        case 'completed_today':
            filtered = filtered.filter(task => task.status === 'Completed' && task.due_date === todayStr);
            break;
        case 'all':
        default:
            break;
    }

    renderTasks(filtered);
}

function renderTasks(tasks) {
    tasksListPending.innerHTML = '';
    tasksListCompleted.innerHTML = '';

    if (tasks.length === 0) {
        tasksListPending.innerHTML = '<p class="text-center text-gray-500">No tasks found.</p>';
        return;
    }

    tasks.forEach(task => {
        const taskEl = document.createElement('div');
        taskEl.className = 'bg-gray-50 border border-gray-200 rounded p-4 flex items-center justify-between cursor-pointer';
        taskEl.dataset.id = task.id;

        const leftDiv = document.createElement('div');
        leftDiv.className = 'flex items-center space-x-4';

        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.checked = task.status === 'Completed';
        checkbox.className = 'w-6 h-6 rounded-full';
        checkbox.addEventListener('change', async () => {
            await toggleStatus(task.id, checkbox.checked);
            await fetchTasks();
        });

        const titleDescDiv = document.createElement('div');
        titleDescDiv.className = 'flex flex-col';

        const title = document.createElement('span');
        title.className = 'font-semibold text-lg ' + (task.status === 'Completed' ? 'line-through text-gray-400' : 'text-gray-800');
        title.textContent = task.title;

        const description = document.createElement('span');
        description.className = 'text-gray-600 text-sm ' + (task.status === 'Completed' ? 'line-through' : '');
        description.textContent = task.description || '';

        titleDescDiv.appendChild(title);
        titleDescDiv.appendChild(description);

        leftDiv.appendChild(checkbox);
        leftDiv.appendChild(titleDescDiv);

        const rightDiv = document.createElement('div');
        rightDiv.className = 'flex items-center space-x-4';

        if (task.due_date) {
            const dueDate = document.createElement('span');
            dueDate.className = 'text-sm text-gray-500';
            dueDate.textContent = formatDate(task.due_date);
            rightDiv.appendChild(dueDate);
        }

        if (task.category_name) {
            const categorySpan = document.createElement('span');
            categorySpan.className = 'text-sm text-blue-600 font-semibold px-2 py-1 rounded bg-blue-100';
            categorySpan.textContent = task.category_name;
            rightDiv.appendChild(categorySpan);
        }

        const priority = document.createElement('span');
        priority.className = 'text-sm font-semibold px-2 py-1 rounded ' +
            (task.priority === 'High' ? 'bg-red-200 text-red-800' :
                task.priority === 'Medium' ? 'bg-yellow-200 text-yellow-800' :
                    'bg-green-200 text-green-800');
        priority.textContent = task.priority;
        rightDiv.appendChild(priority);

        const editBtn = document.createElement('button');
        editBtn.className = 'text-blue-600 hover:text-blue-800';
        editBtn.innerHTML = '<i class="fas fa-edit"></i>';
        editBtn.title = 'Edit Task';
        editBtn.addEventListener('click', () => editTask(task));
        rightDiv.appendChild(editBtn);

        const deleteBtn = document.createElement('button');
        deleteBtn.className = 'text-red-600 hover:text-red-800';
        deleteBtn.innerHTML = '<i class="fas fa-trash-alt"></i>';
        deleteBtn.title = 'Delete Task';
        deleteBtn.addEventListener('click', () => deleteTask(task.id));
        rightDiv.appendChild(deleteBtn);

        taskEl.appendChild(leftDiv);
        taskEl.appendChild(rightDiv);

        if (task.status === 'Pending') {
            tasksListPending.appendChild(taskEl);
        } else {
            tasksListCompleted.appendChild(taskEl);
        }
    });
}

async function toggleStatus(id, completed) {
    await fetch(`tasks.php?id=${id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ status: completed ? 'Completed' : 'Pending' })
    });
}

function editTask(task) {
    taskIdInput.value = task.id;
    titleInput.value = task.title;
    descriptionInput.value = task.description || '';
    startDateInput.value = task.start_date || getTodayStr();
    dueDateInput.value = task.due_date || getTodayStr();
    priorityInput.value = task.priority;
    categorySelect.value = task.category_id || '';
    repetitionSelect.value = 'None';
    setRepetitionDetails(null);
    taskFormModal.classList.remove('hidden');
}

async function deleteTask(id) {
    if (!confirm('Are you sure you want to delete this task?')) return;
    await fetch(`tasks.php?id=${id}`, { method: 'DELETE' });
    await fetchTasks();
}

taskForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = taskIdInput.value;
    const taskData = {
        title: titleInput.value.trim(),
        description: descriptionInput.value.trim(),
        start_date: startDateInput.value || getTodayStr(),
        due_date: dueDateInput.value || getTodayStr(),
        priority: priorityInput.value,
        category_id: categorySelect.value || null,
        repetition: repetitionSelect.value,
        repetition_details: getRepetitionDetails()
    };

    if (!taskData.title) {
        alert('Title is required');
        return;
    }

    if (id) {
        // Update task
        await fetch(`tasks.php?id=${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(taskData)
        });
    } else {
        // Create task
        await fetch('tasks.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(taskData)
        });
    }

    taskForm.reset();
    taskIdInput.value = '';
    repetitionSelect.value = 'None';
    repetitionDetailsDiv.classList.add('hidden');
    weeklyOptions.classList.add('hidden');
    monthlyOptions.classList.add('hidden');
    yearlyOptions.classList.add('hidden');
    taskFormModal.classList.add('hidden');
    await fetchTasks();
});

searchInput.addEventListener('input', () => {
    const filter = searchInput.value.toLowerCase();
    const allTaskElements = [...tasksListPending.children, ...tasksListCompleted.children];
    allTaskElements.forEach(taskEl => {
        const title = taskEl.querySelector('span.font-semibold').textContent.toLowerCase();
        const description = taskEl.querySelector('span.text-gray-600').textContent.toLowerCase();
        if (title.includes(filter) || description.includes(filter)) {
            taskEl.style.display = '';
        } else {
            taskEl.style.display = 'none';
        }
    });
});

// Move to Today button for overdue or future tasks
function addMoveToTodayButtons() {
    const todayStr = getTodayStr();
    [...tasksListPending.children].forEach(taskEl => {
        const dueDateSpan = taskEl.querySelector('span.text-gray-500');
        if (!dueDateSpan) return;
        const dueDate = new Date(dueDateSpan.textContent);
        const today = new Date(todayStr);
        if (dueDate.getTime() !== today.getTime()) {
            if (!taskEl.querySelector('.move-to-today-btn')) {
                const btn = document.createElement('button');
                btn.textContent = 'Move to Today';
                btn.className = 'move-to-today-btn bg-yellow-400 text-white px-2 py-1 rounded ml-4 hover:bg-yellow-500 transition text-sm';
                btn.addEventListener('click', async () => {
                    const id = taskEl.dataset.id;
                    await fetch(`tasks.php?id=${id}`, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ due_date: todayStr })
                    });
                    await fetchTasks();
                });
                taskEl.querySelector('div.flex.items-center.space-x-4').appendChild(btn);
            }
        }
    });
}

// Modify renderTasks to call addMoveToTodayButtons after rendering
const originalRenderTasks = renderTasks;
renderTasks = function(tasks) {
    originalRenderTasks(tasks);
    addMoveToTodayButtons();
};

// Open and close task form modal
openTaskFormBtn.addEventListener('click', () => {
    taskFormModal.classList.remove('hidden');
});

closeTaskFormBtn.addEventListener('click', () => {
    taskFormModal.classList.add('hidden');
});

// Initial load
fetchCategories();
fetchTasks();
</script>
</body>
</html>
