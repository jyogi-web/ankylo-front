import React, { useEffect, useState } from 'react';
import axios from 'axios';

const CardsList = ({ deckId }) => {
    const [cards, setCards] = useState([]);

    useEffect(() => {
        axios.get(`/api/decks/${deckId}/cards`)
            .then(response => {
                setCards(response.data);
            })
            .catch(error => {
                console.error('Error fetching cards:', error);
            });
    }, [deckId]);

    return (
        <div>
            <h3>Deck ID: {deckId}</h3>
            <ul>
                {cards.length > 0 ? (
                    cards.map((card) => (
                        <li key={card.id}> - {card.name} - {card.type} - 偏差値{card.power}</li>
                    ))
                ) : (
                    <li>No cards in this deck</li>
                )}
            </ul>
        </div>
    );
};

export default CardsList;
