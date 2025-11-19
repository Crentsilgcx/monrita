import axios, { AxiosRequestConfig } from 'axios';

const api = axios.create({
  baseURL: process.env.NEXT_PUBLIC_API_BASE ?? 'http://localhost:8000/api/v1',
  timeout: 10_000,
  headers: {
    Accept: 'application/json',
  },
});

const pendingRequests = new Map<string, AbortController>();
const etagCache = new Map<string, string>();

function buildKey(config: AxiosRequestConfig) {
  return [config.method, config.url, JSON.stringify(config.params ?? {}), JSON.stringify(config.data ?? {})].join('|');
}

api.interceptors.request.use((config) => {
  const enriched = config;
  if (typeof window !== 'undefined') {
    const token = localStorage.getItem('monrita_token');
    if (token) {
      enriched.headers = enriched.headers ?? {};
      enriched.headers.Authorization = `Bearer ${token}`;
    }
  }
  if ((enriched.method ?? 'get').toLowerCase() === 'get') {
    const key = buildKey(enriched);
    pendingRequests.get(key)?.abort();
    const controller = new AbortController();
    pendingRequests.set(key, controller);
    enriched.signal = controller.signal;
    (enriched as AxiosRequestConfig & { __requestKey?: string }).__requestKey = key;
    const cachedEtag = etagCache.get(key);
    if (cachedEtag) {
      enriched.headers = enriched.headers ?? {};
      enriched.headers['If-None-Match'] = cachedEtag;
    }
  }
  return enriched;
});

api.interceptors.response.use(
  (response) => {
    const key = (response.config as AxiosRequestConfig & { __requestKey?: string }).__requestKey;
    if (key) {
      pendingRequests.delete(key);
      const responseEtag = response.headers?.etag;
      if (responseEtag) {
        etagCache.set(key, responseEtag as string);
      }
    }
    return response;
  },
  (error) => {
    const key = (error.config as AxiosRequestConfig & { __requestKey?: string })?.__requestKey;
    if (key) {
      pendingRequests.delete(key);
    }
    return Promise.reject(error);
  }
);

export default api;
