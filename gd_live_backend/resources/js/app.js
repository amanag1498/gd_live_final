import './bootstrap';
import { initializeApp } from "firebase/app";
import { getAuth, GoogleAuthProvider, getRedirectResult, signInWithPopup, signInWithRedirect } from "firebase/auth";

const firebaseConfig = window.__FIREBASE_WEB_CONFIG__ ?? {};
const requiredKeys = ['api_key', 'auth_domain', 'project_id', 'app_id'];
const missingKeys = requiredKeys.filter((key) => !firebaseConfig[key]);

const app = missingKeys.length === 0 ? initializeApp({
  apiKey: firebaseConfig.api_key,
  authDomain: firebaseConfig.auth_domain,
  projectId: firebaseConfig.project_id,
  storageBucket: firebaseConfig.storage_bucket || undefined,
  messagingSenderId: firebaseConfig.messaging_sender_id || undefined,
  appId: firebaseConfig.app_id,
}) : null;
const auth = app ? getAuth(app) : null;
const provider = new GoogleAuthProvider();

const btn = document.getElementById('googleLoginBtn');

async function submitFirebaseLogin(idToken) {
  const resp = await fetch('/auth/firebase/login', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
    },
    body: JSON.stringify({ idToken }),
  });

  if (!resp.ok) {
    const text = await resp.text();
    console.error('Server error:', resp.status, text);
    throw new Error('Server login failed: ' + resp.status);
  }

  const data = await resp.json();
  if (data.ok) {
    location.href = data.redirect;
    return;
  }
  throw new Error(data.msg || 'Login failed');
}

async function completeRedirectLogin() {
  if (!auth) return;
  try {
    const result = await getRedirectResult(auth);
    if (!result?.user) return;
    const idToken = await result.user.getIdToken(true);
    await submitFirebaseLogin(idToken);
  } catch (e) {
    console.error('Firebase redirect login error:', e.code, e.message, e);
  }
}

if (auth) {
  void completeRedirectLogin();
}

if (btn && auth) {
  btn.addEventListener('click', async () => {
    try {
      const result = await signInWithPopup(auth, provider);
      const idToken = await result.user.getIdToken(true);
      await submitFirebaseLogin(idToken);
    } catch (e) {
      console.error('Firebase client error:', e.code, e.message, e);
      if (e?.code === 'auth/popup-blocked' || e?.code === 'auth/popup-closed-by-user') {
        try {
          await signInWithRedirect(auth, provider);
          return;
        } catch (redirectError) {
          console.error('Firebase redirect fallback failed:', redirectError.code, redirectError.message, redirectError);
          alert((redirectError.code || 'error') + ': ' + (redirectError.message || 'Login failed'));
          return;
        }
      }
      alert((e.code || 'error') + ': ' + (e.message || 'Login failed'));
    }
  });
} else if (btn && !auth) {
  btn.addEventListener('click', () => {
    alert('Google sign-in is not configured on this environment.');
  });
}
