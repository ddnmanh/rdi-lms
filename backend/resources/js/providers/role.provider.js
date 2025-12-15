
class RoleProvider {

    async handleGetRoles(queryParams = {}) {
        try {
            const data = await apiJsonRequest('/roles', 'GET', null, queryParams);
            return data;
        } catch (error) {
            return { success: false, message: error.message };
        }
    }
}

window.RoleProvider = new RoleProvider();
