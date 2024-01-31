import {Inertia} from "@inertiajs/inertia";
import {nextTick} from "vue";

/**
 * Convert a given date to a normal date format.
 *
 * @param {string} date - The date to be converted.
 * @return {string} The date in the format "DD-MM-YYYY".
 */
export function normalDate(date) {
    if (date) {
        const dateObject = new Date(date);
        const year = dateObject.getFullYear();
        const month = dateObject.getMonth() + 1; // Month is zero-based
        const day = dateObject.getDate();
        const hours = dateObject.getHours();
        const minutes = dateObject.getMinutes();
        return `${day < 10 ? '0' : ''}${day}-${month < 10 ? '0' : ''}${month}-${year} ${hours < 10 ? '0' : ''}${hours}:${minutes < 10 ? '0' : ''}${minutes}`;
    }
}

/**
 * Deletes a user
 */
export function deleteUser(id) {
    Inertia.delete('/user/' + id);
}

/**
 * Deletes a recipe
 */
export function deleteRecipe(id) {
    Inertia.delete('/recipe/' + id);
}

/**
 * Deletes a category
 * @param id
 */
export function deleteCategory(id) {
    Inertia.delete('/category/' + id);
}

/**
 * Deletes an ingredient
 * @param id
 */
export function deleteIngredient(id) {
    Inertia.delete('/ingredient/' + id);
}

/**
 * Deletes a collection
 * @param id
 */
export function deleteCollection(id) {
    Inertia.delete('/collection/' + id);
}

/**
 * Edit a user
 */
export function editUser(id) {
    Inertia.get('/user/' + id + '/edit');
}

/**
 * Edit a recipe
 */
export function editRecipe(id) {
    Inertia.get('/recipe/' + id + '/edit');
}

/**
 * Accepts string and removes leading and trailing quotes and trims the string
 * @param str
 * @returns {*}
 */
export function formatString(str) {
    return str.replace(/^["']+|["']+$/g, '').trim();
}

/**
 * Retrieves the notifications from local storage and sort them by date.
 */
export function getNotifications() {
    if (sessionStorage.getItem('notifications')) {
        let notifications = JSON.parse(sessionStorage.getItem('notifications'));
        return notifications.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
    } else {
        return [];
    }

}

/**
 * Deletes a comment with the specified ID.
 */
export function deleteComment(id) {
    Inertia.delete('/comment/' + id);
}

/**
 * Decides what to delete
 */
export function deleteItem(id, type) {
    switch (type) {
        case 'user':
            this.deleteUser(id);
            break;
        case 'title':
            this.deleteRecipe(id);
            break;
        case 'comment':
            this.deleteComment(id);
            break;
        case 'ingredient':
            this.deleteIngredient(id);
            break;
        case 'category':
            this.deleteCategory(id);
            break;
        case 'collection':
            this.deleteCollection(id);
            break;
        // Add more cases as needed
    }
}

/**
 * Decides what to edit
 */
export function editItem(data, type) {
    let id = data['id'];
    switch (type) {
        case 'user':
            this.editUser(id);
            break;
        case 'recipe':
            this.editRecipe(id);
            break;
        case 'collection':
            this.editCollection(id);
            break;
        // Add more cases as needed
    }
}

/**
 * Update an item
 */
export function updateItem(id, type, editData) {
    switch (type) {
        case 'ingredient':
            this.updateIngredient(id, editData);
            break;
        case 'category':
            this.updateCategory(id, editData);
            break;
        // Add more cases as needed
    }
}

/**
 * Create an item
 */
export function addItem(type, editData) {
    switch (type) {
        case 'ingredient':
            this.addIngredient(editData);
            break;
        case 'category':
            this.addCategory(editData);
            break;
        // Add more cases as needed
    }
}

/**
 * Edit an ingredient
 */
export function updateIngredient(id, editData) {
    Inertia.put('/ingredient/' + id, {
        name: editData
    });
}

/**
 * Edit a category
 */
export function updateCategory(id, editData) {
    Inertia.put('/category/' + id, {
        name: editData
    });
}

/**
 * Edit a collection
 */
export function editCollection(id) {
    Inertia.get('/collection/' + id + '/edit');
}

/**
 * Create a category
 * @param id
 * @param editData
 */
export function addCategory(editData) {
    Inertia.post('/category', {
        name: editData
    });
}

/**
 * Create an ingredient
 * @param id
 * @param editData
 */
export function addIngredient(editData) {
    Inertia.post('/ingredient', {
        name: editData
    });
}

/**
 * Checks if requested active chat exists
 */
export function checkForRequestedActiveChat() {
    return sessionStorage.getItem('openActiveChatForUser');
}

/**
 * Remove request for opening active chat from session storage
 */
export function removeRequestedActiveChat() {
    sessionStorage.removeItem('openActiveChatForUser');
}

/**
 * Get requested active chat
 */
export function getRequestedActiveChat() {
    return JSON.parse(sessionStorage.getItem('openActiveChatForUser'));
}

/**
 * Listen for technical support requests
 */
export function listenForTechnicalSupportRequests() {
    window.Echo.private('technical-support').listen('.technical-support-request', (data) => {
        console.log(data);
        let existingTechnicalSupportRequests = JSON.parse(sessionStorage.getItem('technicalSupportRequests')) || [];
        existingTechnicalSupportRequests = existingTechnicalSupportRequests.filter(item => item.user_id !== data.user_id);
        existingTechnicalSupportRequests.push(data.technicalSupport);
        sessionStorage.setItem('technicalSupportRequests', JSON.stringify(existingTechnicalSupportRequests));
    });
}

/**
 * Retrieve technical support requests from session storage
 * @returns {any}
 */
export function getTechnicalSupportRequests() {
    return JSON.parse(sessionStorage.getItem('technicalSupportRequests')) || [];
}

/**
 * Retrieve technical support requests from session storage
 * @returns {any}
 */
export function setTechnicalSupportRequests(data) {
    sessionStorage.setItem('technicalSupportRequests', JSON.stringify(data));
}

/**
 * Update technical support list from session storage
 * @returns {any}
 */
export function updateTechnicalSupportRequestList(newRequests) {
    let existing = getTechnicalSupportRequests();
    existing.push(newRequests);
    sessionStorage.setItem('technicalSupportRequests', JSON.stringify(existing));
}

/**
 * Decline technical support request
 */
export function declineTechnicalSupportRequest(id, currentStatus) {
    let existingRequests = getTechnicalSupportRequests();
    existingRequests = existingRequests.filter(request => (request.id !== id));
    sessionStorage.setItem('technicalSupportRequests', JSON.stringify(existingRequests));
    sessionStorage.removeItem('technical-' + id);
    sessionStorage.removeItem('activeSupportChat');
}

/**
 * Accept technical support request
 */
export function acceptTechnicalSupportRequest(id, currentStatus) {
    let existingRequests = getTechnicalSupportRequests();
    existingRequests.find(request => (request.id === id)).read_at = new Date();
    existingRequests.find(request => (request.id === id)).tsr_status = currentStatus === null ? 'accepted' : 'processed';
    sessionStorage.setItem('technicalSupportRequests', JSON.stringify(existingRequests));

}

/**
 * Returns accepted technical support requests
 * @returns {*}
 */
export function getAcceptedTechnicalSupportRequests() {
    let existingRequests = getTechnicalSupportRequests();
    return existingRequests.filter(request => (request.tsr_status === 'accepted')) || [];
}

/**
 * Get Messages for support chat
 * @param id
 * @returns {any|*[]}
 */
export function getMessagesForSupportChat(id = null) {
    if (id) {
        return sessionStorage.getItem('technical-' + id) ? JSON.parse(sessionStorage.getItem('technical-' + id)) : [];
    }
    return JSON.parse(sessionStorage.getItem('activeSupportChat')) || [];
}

/**
 * Update support chat messages
 */
export function updateSupportChatMessages(newMessage, id = null) {
    if (id) {
        let existingMessages = getMessagesForSupportChat(id);
        existingMessages.push(newMessage);
        sessionStorage.setItem('technical-' + id, JSON.stringify(existingMessages));
    } else {
        let existingMessages = getMessagesForSupportChat();
        existingMessages.push(newMessage);
        sessionStorage.setItem('activeSupportChat', JSON.stringify(existingMessages));
    }

}
