import ApplicationLogo from '@/Components/ApplicationLogo';
import Dropdown from '@/Components/Dropdown';
import NavLink from '@/Components/NavLink';
import { Link, usePage } from '@inertiajs/react';
import { useState } from 'react';
import { FaHome, FaCogs, FaFistRaised } from 'react-icons/fa'; // アイコン用にreact-iconsをインポート

export default function AuthenticatedLayout({ header, children }) {
    const user = usePage().props.auth.user;

    const [showingNavigationDropdown, setShowingNavigationDropdown] = useState(false);

    return (
        <div className="min-h-screen bg-gray-100 flex flex-col">
            <nav className="fixed bottom-0 w-full bg-white border-t border-gray-100">
                <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div className="flex justify-center space-x-10 py-6"> {/* ナビゲーションバーを大きく */}
                        {/* ホーム */}
                        <NavLink href={route('home')} active={route().current('home')} className="flex flex-col items-center">
                            <FaHome className="text-3xl text-gray-800" /> {/* ホームのアイコン */}
                            <span className="text-lg text-gray-800">ホーム</span>
                        </NavLink>

                        {/* カード */}
                        <NavLink href={route('cards')} active={route().current('cards')} className="flex flex-col items-center">
                            <FaCogs className="text-3xl text-gray-800" /> {/* カードのアイコン */}
                            <span className="text-lg text-gray-800">カード</span>
                        </NavLink>

                        {/* バトル */}
                        <NavLink href={route('battle')} active={route().current('battle')} className="flex flex-col items-center">
                            <FaFistRaised className="text-3xl text-gray-800" /> {/* バトルのアイコン */}
                            <span className="text-lg text-gray-800">バトル</span>
                        </NavLink>
                    </div>
                </div>
            </nav>

            <div className="flex-1">
                <nav className="border-b border-gray-100 bg-white">
                    <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                        <div className="flex h-16 justify-between">
                            <div className="flex items-center">
                                <div className="flex shrink-0 items-center">
                                    <Link href="/">
                                        {/* ここを画像ロゴに変更 */}
                                        <img
                                            src="/images/logo.png" // オリジナルロゴ画像のパス
                                            alt="Game Logo"
                                            className="block h-9 w-auto"
                                        />
                                    </Link>
                                </div>
                                <div className="ml-3 text-2xl font-bold text-gray-800">Shikaku mounting card game pocket</div> {/* タイトルを追加 */}
                            </div>
                            <div className="hidden sm:ms-6 sm:flex sm:items-center">
                                <div className="relative ms-3">
                                    <Dropdown>
                                        <Dropdown.Trigger>
                                            <span className="inline-flex rounded-md">
                                                <button
                                                    type="button"
                                                    className="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none"
                                                >
                                                    {user.name}

                                                    <svg
                                                        className="-me-0.5 ms-2 h-4 w-4"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 20 20"
                                                        fill="currentColor"
                                                    >
                                                        <path
                                                            fillRule="evenodd"
                                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                            clipRule="evenodd"
                                                        />
                                                    </svg>
                                                </button>
                                            </span>
                                        </Dropdown.Trigger>

                                        <Dropdown.Content>
                                            <Dropdown.Link href={route('profile.edit')}>
                                                Profile
                                            </Dropdown.Link>
                                            <Dropdown.Link
                                                href={route('logout')}
                                                method="post"
                                                as="button"
                                            >
                                                Log Out
                                            </Dropdown.Link>
                                        </Dropdown.Content>
                                    </Dropdown>
                                </div>
                            </div>
                        </div>
                    </div>
                </nav>

                {header && (
                    <header className="bg-white shadow">
                        <div className="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                            {header}
                        </div>
                    </header>
                )}

                <main>{children}</main>
            </div>
        </div>
    );
}
