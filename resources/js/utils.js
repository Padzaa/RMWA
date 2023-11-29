import {Inertia} from "@inertiajs/inertia";

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
