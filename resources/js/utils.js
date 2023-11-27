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
