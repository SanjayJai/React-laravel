// src/Lib/axios.js
import axios from "axios";

export const axiosInstance = axios.create({
  baseURL: "http://127.0.0.1:8000/api", // ✅ Backend URL
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
  },
});