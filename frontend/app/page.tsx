import { MobileExperienceSplash } from '../components/mobile/mobile-showcase';
import { ExperienceSummaryList } from '../components/experience/pro-experience';

export default function Home() {
  return (
    <main className="min-h-screen bg-slate-950 text-white">
      <MobileExperienceSplash />
      <section className="mx-auto max-w-3xl px-6 pb-12">
        <ExperienceSummaryList />
      </section>
    </main>
  );
}
