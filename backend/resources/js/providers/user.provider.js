
class UserProvider {

    async handleGetUsers(queryParams = {}) {
        try {
            const data = await apiJsonRequest('/users', 'GET', null, queryParams);
            return data;
        } catch (error) {
            return { success: false, message: error.message };
        }
    }

    async handleGetUserById(userId) {
        try {
            const data = await apiJsonRequest(`/users/${userId}`, 'GET');
            return data;
        } catch (error) {
            return { success: false, message: error.message };
        }
    }

    async handleDeleteUsers(arrayIds = []) {
        try {
            const data = await apiJsonRequest('/users', 'DELETE', {
                user_ids: arrayIds
            });
            return data;
        } catch (error) {
            return { success: false, message: error.message };
        }
    }
}

window.UserProvider = new UserProvider();
