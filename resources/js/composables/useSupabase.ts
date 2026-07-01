import { supabase } from '@/lib/supabase';
import { ref } from 'vue';

export function useSupabase() {
    const loading = ref(false);
    const error = ref<string | null>(null);

    /**
     * Fetch data from a table with optional filters.
     */
    async function from<T = Record<string, unknown>>(
        table: string,
        options?: {
            select?: string;
            eq?: Record<string, unknown>;
            order?: { column: string; ascending?: boolean };
            limit?: number;
            range?: { from: number; to: number };
        },
    ): Promise<{ data: T[] | null; count: number | null }> {
        loading.value = true;
        error.value = null;

        try {
            let query = supabase.from(table).select(options?.select ?? '*', {
                count: 'exact',
            });

            if (options?.eq) {
                for (const [key, value] of Object.entries(options.eq)) {
                    query = query.eq(key, value);
                }
            }

            if (options?.order) {
                query = query.order(options.order.column, {
                    ascending: options.order.ascending ?? true,
                });
            }

            if (options?.limit) {
                query = query.limit(options.limit);
            }

            if (options?.range) {
                query = query.range(options.range.from, options.range.to);
            }

            const { data, count } = await query;

            return { data: (data as T[] | null), count };
        } catch (err) {
            error.value =
                err instanceof Error ? err.message : 'An unknown error occurred';
            return { data: null, count: null };
        } finally {
            loading.value = false;
        }
    }

    /**
     * Insert a single record or multiple records into a table.
     */
    async function insert<T = Record<string, unknown>>(
        table: string,
        values: Partial<T> | Partial<T>[],
    ) {
        loading.value = true;
        error.value = null;

        try {
            const { data, error: insertError } = await supabase
                .from(table)
                .insert(values as never)
                .select();

            if (insertError) throw insertError;

            return { data: data as T[] | null, error: null };
        } catch (err) {
            const msg =
                err instanceof Error ? err.message : 'An unknown error occurred';
            error.value = msg;
            return { data: null, error: msg };
        } finally {
            loading.value = false;
        }
    }

    /**
     * Update records in a table matching a filter.
     */
    async function update<T = Record<string, unknown>>(
        table: string,
        values: Partial<T>,
        match: Record<string, unknown>,
    ) {
        loading.value = true;
        error.value = null;

        try {
            let query = supabase.from(table).update(values as never);

            for (const [key, value] of Object.entries(match)) {
                query = query.eq(key, value);
            }

            const { data, error: updateError } = await query.select();

            if (updateError) throw updateError;

            return { data: data as T[] | null, error: null };
        } catch (err) {
            const msg =
                err instanceof Error ? err.message : 'An unknown error occurred';
            error.value = msg;
            return { data: null, error: msg };
        } finally {
            loading.value = false;
        }
    }

    /**
     * Delete records from a table matching a filter.
     */
    async function remove(
        table: string,
        match: Record<string, unknown>,
    ) {
        loading.value = true;
        error.value = null;

        try {
            let query = supabase.from(table).delete();

            for (const [key, value] of Object.entries(match)) {
                query = query.eq(key, value);
            }

            const { error: deleteError } = await query;

            if (deleteError) throw deleteError;

            return { error: null };
        } catch (err) {
            const msg =
                err instanceof Error ? err.message : 'An unknown error occurred';
            error.value = msg;
            return { error: msg };
        } finally {
            loading.value = false;
        }
    }

    return {
        supabase,
        loading,
        error,
        from,
        insert,
        update,
        remove,
    };
}
