// src/store/useAuthStore.js
import { create } from "zustand";
import { axiosInstance } from "../Lib/axios.js";
import toast from "react-hot-toast";

// hello

export const useAuthStore = create((set) => ({
  authUser: null,
  isSigningUp: false,
  isLoggingIn: false,
  isUpdatingProfile: false,
  isCheckingAuth: true,

  // ✅ SIGNUP
  signup: async (data) => {
    set({ isSigningUp: true });
    try {
      const response = await axiosInstance.post("/auth/signup", {
        name: data.fullName, // Laravel expects "name"
        email: data.email,
        password: data.password,
      });

      set({ authUser: response.data.user });
      toast.success("Account created successfully");
    } catch (error) {
      const msg = error?.response?.data?.message || "Signup failed";
      toast.error(msg);
    } finally {
      set({ isSigningUp: false });
    }
  },

login: async (data) => {
  set({ isLoggingIn: true });
  try {
    const res = await axiosInstance.post("/auth/login", {
      email: data.email,
      password: data.password,
    });

    const { user, token } = res.data;

    localStorage.setItem("token", token); // ✅ store token
    set({ authUser: user });

    toast.success("Logged in successfully");
  } catch (error) {
    toast.error(error?.response?.data?.error || "Login failed");
  } finally {
    set({ isLoggingIn: false });
  }
},


  // ✅ LOGOUT
logout: async () => {
  const token = localStorage.getItem("token");

  try {
    const res = await axiosInstance.post(
      "/auth/logout",
      {},
      {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      }
    );

    // ✅ Extract message from backend
    const message = res.data ? res.data.message : "Logout successful";

    set({ authUser: null });
    localStorage.removeItem("token");

    toast.success(message); // ✅ Use backend message
  } catch (error) {
    toast.error(error?.response?.data?.message || "Logout failed");
  }
},


  // ✅ CHECK AUTH
checkAuth: async () => {
  const token = localStorage.getItem("token");
  if (!token) {
    set({ authUser: null, isCheckingAuth: false });
    return;
  }

  try {
    const res = await axiosInstance.get("/auth/profile", {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    set({ authUser: res.data });
  } catch (error) {
    set({ authUser: null });
  } finally {
    set({ isCheckingAuth: false });
  }
},

}));
