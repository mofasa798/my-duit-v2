export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
}

export interface Category {
    id: number;
    name: string;
    type: 'income' | 'expense';
    user_id: number | null;
}

export interface Transaction {
    id: number;
    user_id: number;
    category_id: number;
    amount: string | number;
    description: string | null;
    date: string;
    created_at: string;
    updated_at: string;
    category?: Category;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
    };
};
