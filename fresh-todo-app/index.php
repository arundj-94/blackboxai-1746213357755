<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Fresh PHP ToDo App</title>
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
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center p-4">
    <div class="flex w-full max-w-6xl bg-white rounded-lg shadow p-6 min-h-[700px]">
        <!-- Sidebar -->
        <nav id="sidebar" class="hidden md:flex flex-col w-72 border-r border-gray-200 pr-6">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Filters</h2>
            <ul class="space-y-3">
                <li>
                    <button data-filter="today" class="filter-btn w-full text-left px-3 py-2 rounded hover:bg-gray-100 transition text-gray-700 font-semibold">Today's Tasks</button>
                </li>
                <li>
                    <button data-filter="completed_today" class="filter-btn w-full text-left px-3 py-2 rounded hover:bg-gray-100 transition text-gray-700 font-semibold">Completed Today</button>
                </li>
                <li>
                    <button data-filter="all" class="filter-btn w-full text-left px-3 py-2 rounded hover:bg-gray-100 transition text-gray-700 font-semibold">All Tasks</button>
                </li>
                <li>
                    <button data-filter="pending" class="filter-btn w-full text-left px-3 py-2 rounded hover:bg-gray-100 transition text-gray-700 font-semibold">Pending Tasks</button>
                </li>
                <li>
                    <button data-filter="overdue" class="filter-btn w-full text-left px-3 py-2 rounded hover:bg-gray-100 transition text-gray-700 font-semibold">Overdue Tasks</button>
                </li>
                <li>
                    <button data-filter="completed" class="filter-btn w-full text-left px-3 py-2 rounded hover:bg-gray-100 transition text-gray-700 font-semibold">Completed Tasks</button>
                </li>
            </ul>

            <h2 class="text-2xl font-bold mt-8 mb-4 text-gray-800">Categories</h2>
            <ul id="category-list" class="space-y-3 overflow-y-auto max-h-[300px]">
                <!-- Categories will be rendered here -->
            </ul>
            <form id="category-form" class="mt-4 flex space-x-2">
                <input type="text" id="new-category" placeholder="New category" class="flex-grow border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">Add</button>
            </form>

            <button id="refresh-repeat" class="mt-6 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Refresh Repeated Tasks</button>
        </nav>

        <!-- Main content -->
    <div class="flex-grow ml-0 md:ml-6 flex flex-col">
        <h1 class="text-4xl font-bold mb-6 text-center md:text-left text-gray-800">Fresh PHP ToDo App</h1>

        <button id="open-task-form-btn" class="mb-6 bg-blue-600 text-white font-semibold rounded px-6 py-2 hover:bg-blue-700 transition max-w-xs mx-auto md:mx-0">Add Task</button>

        <div id="task-form-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
            <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-2xl relative">
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
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
                            <select id="category" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">None</option>
                            </select>
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
                    </div>

                    <div id="repetition-details" class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4 hidden">
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

                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-600 text-white font-semibold rounded px-6 py-2 hover:bg-blue-700 transition">Add Task</button>
                    </div>
                </form>
            </div>
        </div>

            <div id="tasks-list" class="space-y-4 max-w-4xl mx-auto md:mx-0 overflow-y-auto flex-grow">
                <!-- Tasks will be rendered here -->
            </div>
        </div>
    </div>

    <script>
        const taskForm = document.getElementById('task-form');
        const tasksList = document.getElementById('tasks-list');
        const taskIdInput = document.getElementById('task-id');
        const titleInput = document.getElementById('title');
        const descriptionInput = document.getElementById('description');
        const dueDateInput = document.getElementById('due_date');
        const priorityInput = document.getElementById('priority');
        const categorySelect = document.getElementById('category');
        const repetitionSelect = document.getElementById('repetition');

        const sidebar = document.getElementById('sidebar');
        const filterButtons = document.querySelectorAll('.filter-btn');
        const categoryList = document.getElementById('category-list');
        const categoryForm = document.getElementById('category-form');
        const newCategoryInput = document.getElementById('new-category');
        const refreshRepeatBtn = document.getElementById('refresh-repeat');

        let currentFilter = 'today';
        let currentCategoryId = null;

        async function fetchCategories() {
            const res = await fetch('categories.php');
            if (!res.ok) return;
            const categories = await res.json();
            renderCategories(categories);
            populateCategorySelect(categories);
        }

        function renderCategories(categories) {
            categoryList.innerHTML = '';
            const allCatLi = document.createElement('li');
            const allCatBtn = document.createElement('button');
            allCatBtn.textContent = 'All Categories';
            allCatBtn.className = 'w-full text-left px-3 py-2 rounded hover:bg-gray-100 transition text-gray-700 font-semibold';
            allCatBtn.addEventListener('click', () => {
                currentCategoryId = null;
                fetchTasks();
            });
            allCatLi.appendChild(allCatBtn);
            categoryList.appendChild(allCatLi);

            categories.forEach(cat => {
                const li = document.createElement('li');
                const btn = document.createElement('button');
                btn.textContent = cat.name;
                btn.className = 'w-full text-left px-3 py-2 rounded hover:bg-gray-100 transition text-gray-700';
                btn.addEventListener('click', () => {
                    currentCategoryId = cat.id;
                    fetchTasks();
                });
                li.appendChild(btn);
                categoryList.appendChild(li);
            });
        }

        function populateCategorySelect(categories) {
            categorySelect.innerHTML = '<option value="">None</option>';
            categories.forEach(cat => {
                const option = document.createElement('option');
                option.value = cat.id;
                option.textContent = cat.name;
                categorySelect.appendChild(option);
            });
        }

        categoryForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const name = newCategoryInput.value.trim();
            if (!name) return;
            const res = await fetch('categories.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ name })
            });
            if (res.ok) {
                newCategoryInput.value = '';
                fetchCategories();
            } else {
                alert('Failed to add category');
            }
        });

        repetitionSelect.addEventListener('change', () => {
            const val = repetitionSelect.value;
            document.getElementById('repetition-details').classList.add('hidden');
            document.getElementById('weekly-options').classList.add('hidden');
            document.getElementById('monthly-options').classList.add('hidden');
            document.getElementById('yearly-options').classList.add('hidden');

            if (val === 'Weekly') {
                document.getElementById('repetition-details').classList.remove('hidden');
                document.getElementById('weekly-options').classList.remove('hidden');
            } else if (val === 'Monthly') {
                document.getElementById('repetition-details').classList.remove('hidden');
                document.getElementById('monthly-options').classList.remove('hidden');
            } else if (val === 'Yearly') {
                document.getElementById('repetition-details').classList.remove('hidden');
                document.getElementById('yearly-options').classList.remove('hidden');
            }
        });

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
                    document.getElementById('weekly-options').classList.remove('hidden');
                    document.getElementById('repetition-details').classList.remove('hidden');
                    document.querySelectorAll('.weekday-checkbox').forEach(cb => {
                        cb.checked = obj.days.includes(cb.value);
                    });
                } else if (obj.day) {
                    if (document.getElementById('monthly-options').classList.contains('hidden')) {
                        document.getElementById('monthly-options').classList.remove('hidden');
                        document.getElementById('repetition-details').classList.remove('hidden');
                    }
                    document.getElementById('monthly-day').value = obj.day;
                } else if (obj.month && obj.day) {
                    if (document.getElementById('yearly-options').classList.contains('hidden')) {
                        document.getElementById('yearly-options').classList.remove('hidden');
                        document.getElementById('repetition-details').classList.remove('hidden');
                    }
                    document.getElementById('yearly-month').value = obj.month;
                    document.getElementById('yearly-day').value = obj.day;
                }
            } catch (e) {
                // ignore
            }
        }

        async function fetchTasks() {
            let url = 'tasks.php?';
            if (currentFilter === 'today') {
                url += 'today=1&';
            } else if (currentFilter === 'pending') {
                url += 'status=Pending&';
            } else if (currentFilter === 'completed') {
                url += 'status=Completed&';
            } else if (currentFilter === 'overdue') {
                url += 'overdue=1&';
            } else if (currentFilter === 'completed_today') {
                url += 'completed_today=1&';
            }
            if (currentCategoryId) {
                url += 'category_id=' + currentCategoryId + '&';
            }
            const res = await fetch(url);
            const tasks = await res.json();
            renderTasks(tasks);
        }

        function renderTasks(tasks) {
            tasksList.innerHTML = '';
            if (tasks.length === 0) {
                tasksList.innerHTML = '<p class="text-center text-gray-500">No tasks found.</p>';
                return;
            }
            tasks.forEach(task => {
                const taskEl = document.createElement('div');
                taskEl.className = 'bg-gray-50 border border-gray-200 rounded p-4 flex flex-col md:flex-row md:items-center md:justify-between cursor-move';
                taskEl.setAttribute('draggable', 'true');
                taskEl.dataset.id = task.id;

                const leftDiv = document.createElement('div');
                leftDiv.className = 'flex items-center space-x-4';

                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.checked = task.status === 'Completed';
                checkbox.className = 'w-5 h-5';
                checkbox.addEventListener('change', () => toggleStatus(task.id, checkbox.checked));

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
                rightDiv.className = 'flex items-center space-x-4 mt-4 md:mt-0';

                if (task.due_date) {
                    const dueDate = document.createElement('span');
                    dueDate.className = 'text-sm text-gray-500';
                    dueDate.textContent = new Date(task.due_date).toLocaleDateString();
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

                tasksList.appendChild(taskEl);
            });

            addDragAndDrop();
        }

        async function toggleStatus(id, completed) {
            await fetch(`tasks.php?id=${id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ status: completed ? 'Completed' : 'Pending' })
            });
            fetchTasks();
        }

        function editTask(task) {
            taskIdInput.value = task.id;
            titleInput.value = task.title;
            descriptionInput.value = task.description || '';
            dueDateInput.value = task.due_date || '';
            priorityInput.value = task.priority;
            categorySelect.value = task.category_id || '';
            repetitionSelect.value = 'None';
            setRepetitionDetails(null);
            taskForm.querySelector('button[type="submit"]').textContent = 'Update Task';
        }

        async function deleteTask(id) {
            if (!confirm('Are you sure you want to delete this task?')) return;
            await fetch(`tasks.php?id=${id}`, { method: 'DELETE' });
            fetchTasks();
        }

        taskForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = taskIdInput.value;
            const taskData = {
                title: titleInput.value.trim(),
                description: descriptionInput.value.trim(),
                due_date: dueDateInput.value || null,
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
            document.getElementById('repetition-details').classList.add('hidden');
            document.getElementById('weekly-options').classList.add('hidden');
            document.getElementById('monthly-options').classList.add('hidden');
            document.getElementById('yearly-options').classList.add('hidden');
            taskForm.querySelector('button[type="submit"]').textContent = 'Add Task';
            fetchTasks();
        });

        filterButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                currentFilter = btn.getAttribute('data-filter');
                fetchTasks();
            });
        });

        refreshRepeatBtn.addEventListener('click', async () => {
            refreshRepeatBtn.disabled = true;
            refreshRepeatBtn.textContent = 'Refreshing...';
            try {
                const res = await fetch('repeat_tasks.php');
                if (res.ok) {
                    alert('Repeated tasks refreshed successfully.');
                    fetchTasks();
                } else {
                    alert('Failed to refresh repeated tasks.');
                }
            } catch (e) {
                alert('Error refreshing repeated tasks.');
            }
            refreshRepeatBtn.disabled = false;
            refreshRepeatBtn.textContent = 'Refresh Repeated Tasks';
        });

        // Search functionality
        const searchInput = document.createElement('input');
        searchInput.type = 'text';
        searchInput.placeholder = 'Search tasks...';
        searchInput.className = 'mb-4 w-full max-w-4xl mx-auto md:mx-0 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500';
        tasksList.parentNode.insertBefore(searchInput, tasksList);

        searchInput.addEventListener('input', () => {
            const filter = searchInput.value.toLowerCase();
            const taskItems = tasksList.querySelectorAll('div[draggable="true"]');
            taskItems.forEach(item => {
                const title = item.querySelector('span.font-semibold').textContent.toLowerCase();
                const description = item.querySelector('span.text-gray-600').textContent.toLowerCase();
                if (title.includes(filter) || description.includes(filter)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });

        // Move to Today button for tasks with other dates
        function addMoveToTodayButtons() {
            const taskItems = tasksList.querySelectorAll('div[draggable="true"]');
            taskItems.forEach(item => {
                const dueDateSpan = item.querySelector('span.text-gray-500');
                if (!dueDateSpan) return;
                const dueDate = new Date(dueDateSpan.textContent);
                const today = new Date();
                today.setHours(0,0,0,0);
                if (dueDate.getTime() !== today.getTime() && currentFilter !== 'today') {
                    if (!item.querySelector('.move-to-today-btn')) {
                        const btn = document.createElement('button');
                        btn.textContent = 'Move to Today';
                        btn.className = 'move-to-today-btn bg-yellow-400 text-white px-2 py-1 rounded ml-4 hover:bg-yellow-500 transition text-sm';
                        btn.addEventListener('click', async () => {
                            const id = item.dataset.id;
                            await fetch(`tasks.php?id=${id}`, {
                                method: 'PUT',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({ due_date: new Date().toISOString().split("T")[0] })
                            });
                            fetchTasks();
                        });
                        item.querySelector('div.flex.items-center.space-x-4.mt-4.md\\:mt-0').appendChild(btn);
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

        // Drag and drop functionality
        let dragSrcEl = null;

        function handleDragStart(e) {
            dragSrcEl = this;
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/html', this.outerHTML);
            this.classList.add('dragging');
        }

        function handleDragOver(e) {
            if (e.preventDefault) {
                e.preventDefault();
            }
            e.dataTransfer.dropEffect = 'move';
            return false;
        }

        function handleDragEnter() {
            this.classList.add('drag-over');
        }

        function handleDragLeave() {
            this.classList.remove('drag-over');
        }

        function handleDrop(e) {
            if (e.stopPropagation) {
                e.stopPropagation();
            }
            if (dragSrcEl !== this) {
                this.parentNode.removeChild(dragSrcEl);
                const dropHTML = e.dataTransfer.getData('text/html');
                this.insertAdjacentHTML('beforebegin', dropHTML);
                const dropElem = this.previousSibling;
                addDnDHandlers(dropElem);
                updateOrderOnServer();
            }
            this.classList.remove('drag-over');
            return false;
        }

        function handleDragEnd() {
            this.classList.remove('dragging');
            document.querySelectorAll('.drag-over').forEach(el => el.classList.remove('drag-over'));
        }

        function addDnDHandlers(elem) {
            elem.addEventListener('dragstart', handleDragStart, false);
            elem.addEventListener('dragenter', handleDragEnter, false);
            elem.addEventListener('dragover', handleDragOver, false);
            elem.addEventListener('dragleave', handleDragLeave, false);
            elem.addEventListener('drop', handleDrop, false);
            elem.addEventListener('dragend', handleDragEnd, false);
        }

        function addDragAndDrop() {
            const items = tasksList.querySelectorAll('div[draggable="true"]');
            items.forEach(item => addDnDHandlers(item));
        }

        async function updateOrderOnServer() {
            const items = tasksList.querySelectorAll('div[draggable="true"]');
            for (let i = 0; i < items.length; i++) {
                const id = items[i].dataset.id;
                await fetch(`tasks.php?id=${id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ order_index: i })
                });
            }
        }

        // Mobile sidebar toggle
        const mobileSidebarToggle = document.createElement('button');
        mobileSidebarToggle.innerHTML = '<i class="fas fa-bars"></i>';
        mobileSidebarToggle.className = 'md:hidden fixed top-4 left-4 z-50 bg-white p-2 rounded shadow';
        document.body.appendChild(mobileSidebarToggle);

        mobileSidebarToggle.addEventListener('click', () => {
            if (sidebar.classList.contains('hidden')) {
                sidebar.classList.remove('hidden');
            } else {
                sidebar.classList.add('hidden');
            }
        });

        // Initial fetch
        fetchCategories();
        fetchTasks();
    </script>
</body>
</html>
