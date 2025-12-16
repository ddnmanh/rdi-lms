
class CourseProvider {

    async handleCreateCourse(data) {
        try {
            const response = await apiFormDataRequest('/courses', 'POST', data);
            return response;
        } catch (error) {
            return { success: false, message: error.message };
        }
    }

    async handleGetCourseById(courseId) {
        try {
            const data = await apiJsonRequest(`/courses/${courseId}`, 'GET');
            return data;
        } catch (error) {
            return { success: false, message: error.message };
        }
    }

    async handleUpdateCourse(courseId, data) {
        try {
            const response = await apiFormDataRequest(`/courses/${courseId}`, 'PUT', data);
            return response;
        } catch (error) {
            return { success: false, message: error.message };
        }
    }

    async handleAddLessonsToCourse(courseId, data) {
        try {
            const response = await apiJsonRequest(`/courses/${courseId}/lessons/add`, 'POST', data);
            return response;
        } catch (error) {
            return { success: false, message: error.message };
        }
    }

    async handleRemoveLessonsFromCourse(courseId, data) {
        try {
            const response = await apiJsonRequest(`/courses/${courseId}/lessons/remove`, 'POST', data);
            return response;
        } catch (error) {
            return { success: false, message: error.message };
        }
    }

    async handleAddUsersToCourse(courseId, data) {
        try {
            const response = await apiJsonRequest(`/courses/${courseId}/users/add`, 'POST', data);
            return response;
        } catch (error) {
            return { success: false, message: error.message };
        }
    }

    async handleRemoveUsersFromCourse(courseId, data) {
        try {
            const response = await apiJsonRequest(`/courses/${courseId}/users/remove`, 'POST', data);
            return response;
        } catch (error) {
            return { success: false, message: error.message };
        }
    }
}

window.CourseProvider = new CourseProvider();
