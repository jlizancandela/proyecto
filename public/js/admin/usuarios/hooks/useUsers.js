import { useState, useEffect } from 'https://esm.sh/preact@10.19.3/hooks';

export function useUsers() {
  const [users, setUsers] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [page, setPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [search, setSearch] = useState("");

  const loadUsers = async (searchQuery = "", currentPage = 1) => {
    setLoading(true);
    setError(null);

    try {
      const url = searchQuery
        ? `/admin/api/users?search=${encodeURIComponent(searchQuery)}&page=${currentPage}`
        : `/admin/api/users?page=${currentPage}`;

      const response = await fetch(url);
      const data = await response.json();

      setUsers(data.users || []);
      setTotalPages(data.totalPages || 1);
      setPage(currentPage);
    } catch (err) {
      setError("Error al cargar usuarios");
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    loadUsers();
  }, []);

  useEffect(() => {
    const timer = setTimeout(() => {
      loadUsers(search, 1);
    }, 500);

    return () => clearTimeout(timer);
  }, [search]);

  const deleteUser = async (userId) => {
    try {
      const response = await fetch(`/admin/api/users/${userId}`, {
        method: "DELETE",
      });

      const result = await response.json();

      if (result.success) {
        loadUsers(search, page);
        return true;
      } else {
        throw new Error(result.error || "No se pudo eliminar");
      }
    } catch (err) {
      throw err;
    }
  };

  const changePage = (newPage) => {
    loadUsers(search, newPage);
  };

  return {
    users,
    loading,
    error,
    page,
    totalPages,
    search,
    setSearch,
    deleteUser,
    changePage,
    reload: () => loadUsers(search, page),
  };
}
