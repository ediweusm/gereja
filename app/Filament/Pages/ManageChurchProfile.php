<?php

namespace App\Filament\Pages;

use App\Models\ChurchProfile;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class ManageChurchProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Akses dan Keamanan';

    protected static ?string $navigationLabel = 'Profil Gereja';

    protected static ?string $title = 'Profil Gereja';

    protected static string $view = 'filament.pages.manage-church-profile';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->hasRole('super_admin');
    }

    public function mount(): void
    {
        $profile = ChurchProfile::first();
        
        $this->form->fill($profile?->toArray() ?? []);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Identitas Gereja')
                    ->description('Kelola profil identitas gereja untuk kebutuhan cetak bukti dan kop surat.')
                    ->schema([
                        TextInput::make('gmit_name')
                            ->label('Nama Klasis / Sinode (GMIT)')
                            ->placeholder('Contoh: Majelis Sinode GMIT')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('church_name')
                            ->label('Nama Jemaat / Gereja')
                            ->placeholder('Contoh: Jemaat Sion Oepura')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('address')
                            ->label('Alamat Lengkap')
                            ->placeholder('Masukkan alamat lengkap gereja...')
                            ->required()
                            ->rows(3),
                        TextInput::make('phone')
                            ->label('Nomor Telepon')
                            ->tel()
                            ->placeholder('Contoh: 081123456789')
                            ->required()
                            ->maxLength(50),
                        TextInput::make('ketua_majelis')
                            ->label('Nama Ketua Majelis Jemaat')
                            ->placeholder('Pdt. Nama Lengkap, S.Th')
                            ->maxLength(255),

                        TextInput::make('sekretaris')
                            ->label('Nama Sekretaris')
                            ->placeholder('Pnt. Nama Lengkap')
                            ->maxLength(255),

                        TextInput::make('bendahara')
                            ->label('Nama Bendahara')
                            ->placeholder('Dkn. Nama Lengkap')
                            ->maxLength(255),
                            
                        FileUpload::make('logo_path')
                            ->label('Logo Gereja')
                            ->image()
                            ->directory('logos')
                            ->maxSize(2048)
                            ->nullable(),

                        FileUpload::make('hero_image_path')
                            ->label('Gambar Latar Beranda (Hero Image)')
                            ->image()
                            ->directory('profiles')
                            ->columnSpanFull(),

                        Textarea::make('hero_quote')
                            ->label('Kutipan Beranda / Visi Misi')
                            ->placeholder('Masukkan kalimat sambutan atau visi misi...')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        ChurchProfile::updateOrCreate(
            ['id' => 1],
            $state
        );

        Notification::make()
            ->title('Profil Gereja berhasil disimpan')
            ->success()
            ->send();
    }
}
