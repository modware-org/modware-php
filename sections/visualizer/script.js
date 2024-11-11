document.addEventListener('DOMContentLoaded', () => {
    const containers = document.querySelectorAll('.sections-container');

    containers.forEach(container => {
        container.addEventListener('dragstart', (e) => {
            if (e.target.classList.contains('draggable')) {
                e.target.classList.add('dragging');
                e.dataTransfer.setData('text/plain', e.target.dataset.sectionId);
            }
        });

        container.addEventListener('dragend', (e) => {
            if (e.target.classList.contains('draggable')) {
                e.target.classList.remove('dragging');
            }
        });

        container.addEventListener('dragover', (e) => {
            e.preventDefault();
            const draggingItem = document.querySelector('.dragging');
            const afterElement = getDragAfterElement(container, e.clientY);
            
            if (afterElement == null) {
                container.appendChild(draggingItem);
            } else {
                container.insertBefore(draggingItem, afterElement);
            }
        });

        container.addEventListener('drop', (e) => {
            e.preventDefault();
            const draggingItem = document.querySelector('.dragging');
            const pageId = container.closest('.page-sections').dataset.pageId;
            const sectionId = draggingItem.dataset.sectionId;

            // Recalculate positions
            const sections = Array.from(container.querySelectorAll('.draggable'));
            sections.forEach((section, index) => {
                section.dataset.position = index;
                section.querySelector('.position-value').textContent = index;
            });

            // Send AJAX request to update position
            const newPosition = draggingItem.dataset.position;
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=update_section_position&section_id=${sectionId}&new_position=${newPosition}&page_id=${pageId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Section position updated');
                } else {
                    console.error('Failed to update section position');
                }
            });
        });
    });

    function getDragAfterElement(container, y) {
        const draggableElements = [...container.querySelectorAll('.draggable:not(.dragging)')];

        return draggableElements.reduce((closest, child) => {
            const box = child.getBoundingClientRect();
            const offset = y - box.top - box.height / 2;
            
            if (offset < 0 && offset > closest.offset) {
                return { offset: offset, element: child };
            } else {
                return closest;
            }
        }, { offset: Number.NEGATIVE_INFINITY }).element;
    }
});
