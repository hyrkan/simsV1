// Role translation mappings
export const roleTranslations = {
    'super-admin': 'Super Admin',
    'admin': 'Admin',
    'staff': 'Staff',
    'teacher': 'Teacher',
    'manager': 'Manager',
    'user': 'User',
    'student': 'Student',
    'moderator': 'Moderator'
};

// Function to get translated role name
export function translateRole(role) {
    return roleTranslations[role] || role.charAt(0).toUpperCase() + role.slice(1);
}