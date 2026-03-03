export type OptionItem = {
  id: number;
  label: string;
}

export type OptionsResponse = {
  options: OptionItem[];
  pagination?: {
    page: number;
    limit: number;
    total: number;
    hasNext: boolean;
  };
};