export type Brand = {
    name: string;
    legal_name: string;
    meaning: string;
    domain: string;
    email: string;
    phone: string;
    descriptor: string;
    hero_kicker: string;
    hero_alternative: string;
    on_site_areas: string[];
    remote_coverage: string;
    social: Array<{ label: string; url: string }>;
    opening_hours: string;
};

export type AuthUser = {
    id: number;
    name: string;
    email: string;
    role: string | null;
};

export type PageProps = {
    auth: { user: AuthUser | null };
    brand: Brand;
    flash: { success?: string | null; error?: string | null };
};

export type Paginator<T> = {
    data: T[];
    links?: Array<{ url: string | null; label: string; active: boolean }>;
};
