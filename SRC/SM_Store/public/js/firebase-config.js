// Import the functions you need from the SDKs you need
import { initializeApp } from 'https://www.gstatic.com/firebasejs/10.1.0/firebase-app.js';
import { getAuth } from 'https://www.gstatic.com/firebasejs/10.1.0/firebase-auth.js';
import { getFirestore } from 'https://www.gstatic.com/firebasejs/10.1.0/firebase-firestore.js';

// Your web app's Firebase configuration
// TODO: Thay thế bằng config từ Firebase Console > Project Settings > General > Your apps > Web app
const firebaseConfig = {
  apiKey: "AIzaSyDQVfKeMgqbPbf7qJ9XQ-K5Y5XJZVQa0Cs", // Cần lấy từ Firebase Console
  authDomain: "kchip-8865d.firebaseapp.com",
  projectId: "kchip-8865d",
  storageBucket: "kchip-8865d.appspot.com",
  messagingSenderId: "123456789", // Cần lấy từ Firebase Console
  appId: "1:123456789:web:abcd1234" // Cần lấy từ Firebase Console
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);

// Initialize Firebase Authentication and get a reference to the service
export const auth = getAuth(app);

// Initialize Cloud Firestore and get a reference to the service
export const db = getFirestore(app);

export default app;