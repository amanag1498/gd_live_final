import './bootstrap';
import { initializeApp } from "firebase/app";
import { getAuth, GoogleAuthProvider, signInWithPopup } from "firebase/auth";

const firebaseConfig = {
  apiKey: "AIzaSyBKah5-8pUcKWxgdzj-_fNHvQQOM3-w7cE",
  authDomain: "gdlive-da4e9.firebaseapp.com",
  projectId: "gdlive-da4e9",
  storageBucket: "gdlive-da4e9.firebasestorage.app",
  messagingSenderId: "826349753111",
  appId: "1:826349753111:web:2adc70ba87fef57482dd52"
};

const app = initializeApp(firebaseConfig);
const auth = getAuth(app);
const provider = new GoogleAuthProvider();

const btn = document.getElementById('googleLoginBtn');

if (btn) {
  btn.addEventListener('click', async () => {
    try {
      const result = await signInWithPopup(auth, provider);
      const idToken = await result.user.getIdToken(true);

      const resp = await fetch('/auth/firebase/login', {
        method: 'POST',
        headers: {
          'Content-Type':'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ idToken })
      });

      if (!resp.ok) {
        const text = await resp.text();
        console.error('Server error:', resp.status, text);
        alert('Server login failed: ' + resp.status);
        return;
      }

      const data = await resp.json();
      if (data.ok) location.href = data.redirect;
    } catch (e) {
      console.error('Firebase client error:', e.code, e.message, e);
      alert((e.code || 'error') + ': ' + (e.message || 'Login failed'));
    }
  });
}
