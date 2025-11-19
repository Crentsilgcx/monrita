import withPWA from 'next-pwa';

const isDev = process.env.NODE_ENV !== 'production';

const nextConfig = withPWA({
  dest: 'public',
  disable: isDev,
})({
  reactStrictMode: true,
  experimental: {
    appDir: true,
  },
  images: {
    remotePatterns: [
      {
        protocol: 'https',
        hostname: 'images.unsplash.com',
      },
      {
        protocol: 'https',
        hostname: 'cdn.jsdelivr.net',
      },
      {
        protocol: 'https',
        hostname: 'cdn-icons-png.flaticon.com',
      },
    ],
  },
});

export default nextConfig;
