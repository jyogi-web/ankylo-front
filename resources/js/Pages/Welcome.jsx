// Welcome.jsx

import { Head, Link } from '@inertiajs/react';

export default function Welcome() {
    return (
        <>
            <Head title="Welcome" />
            <Link href="/dashboard" className="min-h-screen bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center cursor-pointer">
                <div>
                    <h1 className="text-4xl sm:text-6xl font-extrabold text-white text-center px-4">
                        ShikakuMountingCardBattlePokets
                    </h1>

                    <h2 className="text-2xl sm:text-4xl font-extrabold text-white text-center px-4">
                        タップでスタート
                    </h2>
                </div>
            </Link>
        </>
    );
}
