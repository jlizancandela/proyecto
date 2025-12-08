import { useState, useEffect, useCallback } from "https://esm.sh/preact@10.19.3/hooks";

export function useUsers() {
  const [state, setState] = useState({
    users: [],
    loading: true,
    error: null,
    page: 1,
    totalPages: 1,
  });
  const [search, setSearch] = useState("");

  const loadUsers = useCallback(async (searchQuery = "", currentPage = 1) => {
    setState((prev) => ({ ...prev, loading: true, error: null }));

    try {
      const params = new URLSearchParams({ page: currentPage });
      if (searchQuery) params.set("search", searchQuery);

      const response = await fetch(`/admin/api/users?${params}`);
      const data = await response.json();

      setState({
        users: data.users || [],
        totalPages: data.totalPages || 1,
        page: currentPage,
        loading: false,
        error: null,
      });
    } catch (err) {
      setState((prev) => ({
        ...prev,
        loading: false,
        error: "Error al cargar usuarios",
      }));
      console.error(err);
    }
  }, []);

  useEffect(() => loadUsers(), [loadUsers]);

  useEffect(() => {
    const timer = setTimeout(() => loadUsers(search, 1), 500);
    return () => clearTimeout(timer);
  }, [search, loadUsers]);

  const deleteUser = async (userId) => {
    const response = await fetch(`/admin/api/users/${userId}`, {
      method: "DELETE",
    });

    const result = await response.json();

    if (!result.success) {
      throw new Error(result.error || "No se pudo eliminar");
    }

    loadUsers(search, state.page);
  };

  return {
    ...state,
    search,
    setSearch,
    deleteUser,
    changePage: (newPage) => loadUsers(search, newPage),
    reload: () => loadUsers(search, state.page),
  };
}
