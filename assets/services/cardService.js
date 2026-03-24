export async function fetchAllCards(setCode = null, page = 1) {
    const url = setCode ? `/api/card/all?setCode=${setCode}&page=${page}` : `/api/card/all?page=${page}`;
    const response = await fetch(url);
    if (!response.ok) throw new Error('Failed to fetch cards');
    const result = await response.json();
    return result;
}

export async function fetchCard(uuid) {
    const response = await fetch(`/api/card/${uuid}`);
    if (response.status === 404) return null;
    if (!response.ok) throw new Error('Failed to fetch card');
    const card = await response.json();
    card.text = card.text.replaceAll('\\n', '\n');
    return card;
}

export async function fetchSearchCard(name, setCode = null) {
    const url = setCode ? `/api/card/search/${name}?setCode=${setCode}` : `/api/card/search/${name}`;
    const response = await fetch(url);
    if (response.status === 404) return null;
    if (!response.ok) throw new Error('Failed to fetch card');
    const card = await response.json();
    return card;
}

export async function fetchSetCodes() {
    const response = await fetch('/api/card/sets');
    if (!response.ok) throw new Error('Failed to fetch set codes');
    const result = await response.json();
    return result;
}
