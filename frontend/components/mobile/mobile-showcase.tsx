import Image from 'next/image';
import Link from 'next/link';
import type { ReactNode } from 'react';

const heroImage =
  'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=640&q=80&sat=-20&exp=-10';
const deviceShot =
  'https://cdn.jsdelivr.net/gh/creativetimofficial/public-assets/argon-dashboard-pro/assets/img/ill/ill-3d-balancing.png';
const iconSource = 'https://cdn-icons-png.flaticon.com/512/992/992700.png';
const iconRealtime = 'https://cdn-icons-png.flaticon.com/512/4144/4144334.png';
const iconShield = 'https://cdn-icons-png.flaticon.com/512/9422/9422185.png';
const remote3DLink = 'https://sketchfab.com/3d-models/modern-warehouse-hub-dash-3d9f21b39e6d4ccd8b7ac3c2b5e8b143d';

export function MobileExperienceSplash() {
  const featureItems = [
    {
      title: 'Touch-optimized cards',
      description: 'Swipe-ready stats and biometric prompts sized for thumbs.',
      icon: iconSource,
    },
    {
      title: 'Realtime telemetry',
      description: 'Live sockets stream deliveries from 20k+ agents without drops.',
      icon: iconRealtime,
    },
    {
      title: 'Offline smart queue',
      description: 'Auto-sync deliveries captured in remote communities.',
      icon: iconShield,
    },
  ];

  const tickerItems = [
    { label: 'North Ridge PS', detail: '1,240 bags', status: 'Incoming · 2m ago' },
    { label: 'Lakeview Academy', detail: '860 cartons', status: 'Delivered · 8m ago' },
    { label: 'Savana Girls', detail: '430 kits', status: 'Verified · 15m ago' },
  ];

  const stats = [
    { label: 'Mobile uptime', value: '99.98%' },
    { label: 'Offline captures', value: '12,480+' },
    { label: 'Avg sync latency', value: '1.2s' },
  ];

  const avatarSources = [
    'https://images.unsplash.com/photo-1544723795-3fb6469f5b39?auto=format&fit=crop&w=200&q=80',
    'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=200&q=80',
    'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=200&q=80',
  ];

  return (
    <MobileSplashShell>
      <div className="flex flex-col gap-8">
        <HeroLogo />
        <HeroTitle text="Precision logistics in your pocket" />
        <HeroSubtitle text="Install the Monrita PWA to capture deliveries, monitor supply plans, and explore immersive dashboards built for field operations." />
        <HeroCTAGroup>
          <HeroCTAButton href="/(auth)/login" label="Launch console" variant="primary" />
          <HeroCTAButton href={remote3DLink} label="View 3D walkthrough" variant="secondary" external />
        </HeroCTAGroup>
        <SocialProof avatars={avatarSources} />
      </div>

      <DeviceMockup heroImage={heroImage} deviceImage={deviceShot} sourceLink={remote3DLink} />

      <DeliveryTicker items={tickerItems} />
      <StatsCluster stats={stats} />
      <FeatureList items={featureItems} />
      <div className="mt-6 flex flex-col items-center gap-3 sm:flex-row sm:justify-center">
        <AppStoreBadge />
        <PlayStoreBadge />
      </div>
      <FloatingActionHint />
    </MobileSplashShell>
  );
}

type ShellProps = { children: ReactNode };

const MobileSplashShell = ({ children }: ShellProps) => (
  <section className="relative isolate overflow-hidden bg-slate-950 px-4 py-16 text-white sm:px-8">
    <GradientBackdrop />
    <AnimatedGrid />
    <OrbitalGlow />
    <div className="relative z-10 mx-auto flex w-full max-w-5xl flex-col gap-10 rounded-3xl border border-white/10 bg-white/5 p-8 shadow-[0_30px_80px_rgba(10,10,10,0.5)] backdrop-blur-xl">
      {children}
    </div>
  </section>
);

const GradientBackdrop = () => (
  <div className="absolute inset-0 bg-gradient-to-b from-brand via-slate-900 to-slate-950" aria-hidden />
);

const AnimatedGrid = () => (
  <div className="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_1px_1px,#ffffff10,transparent_0)] bg-[length:24px_24px] opacity-40" aria-hidden />
);

const OrbitalGlow = () => (
  <div className="pointer-events-none absolute -top-32 right-10 h-72 w-72 rounded-full bg-gold/30 blur-3xl" aria-hidden />
);

const HeroLogo = () => (
  <div className="flex items-center gap-3 text-left">
    <span className="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-white/10 text-2xl">📦</span>
    <div>
      <p className="text-xs uppercase tracking-[0.3em] text-gold">Monrita</p>
      <p className="text-lg font-semibold text-white">Supply Intelligence Suite</p>
    </div>
  </div>
);

type TextProps = { text: string };

const HeroTitle = ({ text }: TextProps) => (
  <h1 className="text-4xl font-black leading-tight text-white sm:text-5xl">{text}</h1>
);

const HeroSubtitle = ({ text }: TextProps) => (
  <p className="text-base text-slate-200 sm:text-lg">{text}</p>
);

type HeroCTAButtonProps = {
  href: string;
  label: string;
  variant?: 'primary' | 'secondary';
  external?: boolean;
};

const HeroCTAGroup = ({ children }: ShellProps) => (
  <div className="flex flex-col gap-3 sm:flex-row">
    {children}
  </div>
);

const HeroCTAButton = ({ href, label, variant = 'primary', external = false }: HeroCTAButtonProps) => (
  <Link
    href={href}
    target={external ? '_blank' : undefined}
    rel={external ? 'noreferrer' : undefined}
    className={`flex-1 rounded-2xl border px-6 py-4 text-center text-sm font-semibold uppercase tracking-wide transition hover:-translate-y-0.5 ${
      variant === 'primary'
        ? 'border-transparent bg-gold text-slate-900'
        : 'border-white/30 bg-white/10 text-white'
    }`}
  >
    {label}
  </Link>
);

type DeviceMockupProps = {
  heroImage: string;
  deviceImage: string;
  sourceLink: string;
};

const DeviceMockup = ({ heroImage: backgroundImage, deviceImage, sourceLink }: DeviceMockupProps) => (
  <div className="mx-auto w-full max-w-sm">
    <div className="relative rounded-[3rem] border border-white/20 bg-black/70 p-6 shadow-[0_30px_90px_rgba(0,0,0,0.8)]">
      <DeviceHeader />
      <DeviceScreen imageUrl={backgroundImage} overlayUrl={deviceImage} sourceLink={sourceLink} />
      <DeviceFooter />
    </div>
  </div>
);

const DeviceHeader = () => (
  <div className="mb-4 flex items-center justify-between rounded-2xl bg-white/5 px-4 py-2 text-xs text-white/70">
    <span>LTE · 09:32</span>
    <div className="flex items-center gap-1">
      {[0, 1, 2].map((idx) => (
        <DeviceStatusDot key={idx} index={idx} />
      ))}
    </div>
  </div>
);

type DeviceStatusDotProps = { index: number };

const DeviceStatusDot = ({ index }: DeviceStatusDotProps) => (
  <span
    className={`h-2 w-2 rounded-full ${index === 2 ? 'bg-emerald-400' : 'bg-white/40'}`}
  />
);

type DeviceScreenProps = {
  imageUrl: string;
  overlayUrl: string;
  sourceLink: string;
};

const DeviceScreen = ({ imageUrl, overlayUrl, sourceLink }: DeviceScreenProps) => (
  <div className="relative overflow-hidden rounded-3xl border border-white/10">
    <Image
      src={imageUrl}
      alt="Mobile field operations"
      width={400}
      height={720}
      className="h-80 w-full object-cover"
      priority
    />
    <div className="absolute inset-x-4 bottom-4 rounded-2xl bg-white/90 p-4 text-slate-900">
      <p className="text-xs font-semibold uppercase text-brand">Immersive 3D supply center</p>
      <div className="mt-2 flex items-center gap-3">
        <Image src={overlayUrl} alt="3D warehouse" width={64} height={64} className="h-12 w-12 object-contain" />
        <div>
          <p className="text-sm font-semibold">Interact with real-world replicas</p>
          <Link href={sourceLink} target="_blank" rel="noreferrer" className="text-xs text-brand underline">
            Visit online 3D scene
          </Link>
        </div>
      </div>
    </div>
  </div>
);

const DeviceFooter = () => (
  <div className="mt-4 rounded-2xl bg-white/5 px-4 py-3 text-xs text-white/80">
    Field staff confirm GPS pins, biometric sign-off, and delivery photos before syncing to HQ.
  </div>
);

type DeliveryTickerProps = {
  items: Array<{ label: string; detail: string; status: string }>;
};

const DeliveryTicker = ({ items }: DeliveryTickerProps) => (
  <div className="grid gap-3 sm:grid-cols-3">
    {items.map((item) => (
      <TickerItem key={item.label} label={item.label} detail={item.detail} status={item.status} />
    ))}
  </div>
);

type TickerItemProps = {
  label: string;
  detail: string;
  status: string;
};

const TickerItem = ({ label, detail, status }: TickerItemProps) => (
  <div className="rounded-2xl border border-white/10 bg-black/30 p-4">
    <p className="text-sm text-slate-300">{label}</p>
    <p className="text-2xl font-bold text-white">{detail}</p>
    <p className="text-xs uppercase text-gold">{status}</p>
  </div>
);

type FeatureListProps = {
  items: Array<{ title: string; description: string; icon: string }>;
};

const FeatureList = ({ items }: FeatureListProps) => (
  <div className="grid gap-4 sm:grid-cols-3">
    {items.map((feature) => (
      <FeatureRow key={feature.title} {...feature} />
    ))}
  </div>
);

type FeatureRowProps = {
  title: string;
  description: string;
  icon: string;
};

const FeatureRow = ({ title, description, icon }: FeatureRowProps) => (
  <div className="rounded-2xl border border-white/10 bg-white/5 p-4">
    <FeatureIcon icon={icon} />
    <p className="mt-3 text-base font-semibold text-white">{title}</p>
    <p className="text-sm text-slate-200">{description}</p>
  </div>
);

type FeatureIconProps = { icon: string };

const FeatureIcon = ({ icon }: FeatureIconProps) => (
  <div className="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/10">
    <Image src={icon} alt="Feature icon" width={32} height={32} className="h-8 w-8" />
  </div>
);

type StatsClusterProps = {
  stats: Array<{ label: string; value: string }>;
};

const StatsCluster = ({ stats }: StatsClusterProps) => (
  <div className="grid gap-4 sm:grid-cols-3">
    {stats.map((stat) => (
      <StatTile key={stat.label} {...stat} />
    ))}
  </div>
);

type StatTileProps = {
  label: string;
  value: string;
};

const StatTile = ({ label, value }: StatTileProps) => (
  <div className="rounded-2xl border border-white/10 bg-black/30 p-4">
    <MetricStack label={label} value={value} />
    <p className="text-xs text-slate-400">Real-time from device sensors</p>
  </div>
);

type MetricStackProps = {
  label: string;
  value: string;
};

const MetricStack = ({ label, value }: MetricStackProps) => (
  <div>
    <p className="text-xs uppercase tracking-[0.4em] text-slate-300">{label}</p>
    <p className="text-3xl font-bold text-gold">{value}</p>
  </div>
);

const AppStoreBadge = () => (
  <Link
    href="https://apps.apple.com"
    target="_blank"
    rel="noreferrer"
    className="flex w-full items-center justify-center gap-3 rounded-2xl border border-white/20 bg-black/40 px-6 py-4 text-sm font-semibold"
  >
    <Image src="https://cdn-icons-png.flaticon.com/512/831/831276.png" alt="Apple" width={28} height={28} />
    App Store Preview
  </Link>
);

const PlayStoreBadge = () => (
  <Link
    href="https://play.google.com"
    target="_blank"
    rel="noreferrer"
    className="flex w-full items-center justify-center gap-3 rounded-2xl border border-white/20 bg-black/40 px-6 py-4 text-sm font-semibold"
  >
    <Image src="https://cdn-icons-png.flaticon.com/512/888/888857.png" alt="Google Play" width={28} height={28} />
    Google Play Preview
  </Link>
);

type SocialProofProps = { avatars: string[] };

const SocialProof = ({ avatars }: SocialProofProps) => (
  <div className="flex flex-col gap-2 text-left">
    <AvatarStack avatars={avatars} />
    <p className="text-sm text-slate-200">
      Loved by frontline logistics teams across 18 countries.
    </p>
  </div>
);

type AvatarStackProps = { avatars: string[] };

const AvatarStack = ({ avatars }: AvatarStackProps) => (
  <div className="flex -space-x-3">
    {avatars.map((src, index) => (
      <AvatarBubble key={src} src={src} index={index} />
    ))}
  </div>
);

type AvatarBubbleProps = { src: string; index: number };

const AvatarBubble = ({ src, index }: AvatarBubbleProps) => (
  <Image
    src={src}
    alt={`Team member ${index + 1}`}
    width={48}
    height={48}
    className="h-12 w-12 rounded-full border-2 border-slate-900 object-cover"
  />
);

const FloatingActionHint = () => (
  <div className="relative flex items-center justify-center">
    <PulseDot />
    <p className="rounded-full bg-white/10 px-5 py-2 text-xs uppercase tracking-[0.4em] text-white">
      Swipe up to install the PWA
    </p>
  </div>
);

const PulseDot = () => (
  <span className="absolute left-4 h-3 w-3 animate-ping rounded-full bg-gold" aria-hidden />
);
