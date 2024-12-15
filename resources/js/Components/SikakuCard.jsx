import React from 'react';

export default function SikakuCard({
    rank,
    power,
    title,
    genre,
    weakGenre,
    description,
}) {
    return (
        <div className="w-60 h-80 bg-white border border-gray-300 rounded-lg shadow-lg overflow-hidden">
            {/* カードヘッダー */}
            <div className="bg-blue-500 text-white text-center py-3">
                <div className="text-2xl font-bold">{rank}</div>
                <div className="text-sm uppercase tracking-wider">Power</div>
                <div className="text-3xl font-extrabold">{power}</div>
            </div>

            {/* カード本体 */}
            <div className="p-4">
                <h2 className="text-xl font-bold text-gray-800">{title}</h2>
                <p className="text-sm text-gray-600 mt-1">
                    ジャンル: <span className="font-semibold">{genre}</span>
                </p>
                <p className="text-sm text-gray-600 mt-1">
                    弱点: <span className="font-semibold">{weakGenre}</span>
                </p>

                {/* 説明 */}
                <p className="text-gray-700 text-sm mt-3 leading-relaxed">
                    {description}
                </p>
            </div>
        </div>
    );
}
