<?php

declare(strict_types=1);

/**
 * @var \App\Models\Utilisateur[] $utilisateurs
 * @var \stdClass[] $roles
 * @var array{total: int, admins: int, gerants: int, actifs: int} $stats
 * @var int $page
 * @var int $totalPages
 * @var int $total
 * @var string $motCle
 * @var int|null $roleId
 * @var string|null $actif (null | '1' | '0')
 * @var int $utilisateurConnecteId
 * @var string $baseRoute (/admin)
 * @var string $mode ('' | creer | modifier)
 * @var \App\Models\Utilisateur|null $utilisateur
 * @var array<string, string> $old
 * @var array<string, string> $errors
 */

$lienBase = $base . $baseRoute . '/utilisateurs';
$mode = $mode ?? '';
$utilisateur = $utilisateur ?? null;
$old = $old ?? [];
$errors = $errors ?? [];

$vPrenom = $old['prenom'] ?? ($utilisateur?->prenom ?? '');
$vNom = $old['nom'] ?? ($utilisateur?->nom ?? '');
$vEmail = $old['email'] ?? ($utilisateur?->email ?? '');
$vRole = $old['role_id'] ?? ($utilisateur !== null ? (string) $utilisateur->roleId : '');
$actifInitiale = (isset($old['actif']) && (string) $old['actif'] === '1')
    || (!isset($old['actif']) && ($utilisateur === null || $utilisateur->actif));
?>

<!-- ============================== EN-TETE ============================== -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Utilisateurs internes</h2>
        <p class="text-sm text-gray-500 mt-1">Gérez les comptes du personnel (administrateurs et gérants) de Saveur 221.</p>
    </div>
    <a href="<?= $lienBase ?>/creer"
       class="inline-flex items-center justify-center gap-2 bg-brand-orange text-white px-5 py-2.5 rounded-lg text-sm font-semibold shadow-sm hover:opacity-90 transition whitespace-nowrap">
        <i class="fa-solid fa-plus"></i> Ajouter un utilisateur
    </a>
</div>

<!-- ============================== CARTES STATISTIQUES ============================== -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 border-l-4 border-l-brand-orange p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Total utilisateurs</p>
                <p class="mt-3 text-3xl font-bold text-gray-900"><?= (int) $stats['total'] ?></p>
            </div>
            <span class="w-10 h-10 rounded-full bg-brand-orange/10 flex items-center justify-center text-brand-orange">
                <i class="fa-solid fa-users"></i>
            </span>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 border-l-4 border-l-blue-500 p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Administrateurs</p>
                <p class="mt-3 text-3xl font-bold text-gray-900"><?= (int) $stats['admins'] ?></p>
            </div>
            <span class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                <i class="fa-solid fa-user-shield"></i>
            </span>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 border-l-4 border-l-amber-400 p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Gérants</p>
                <p class="mt-3 text-3xl font-bold text-gray-900"><?= (int) $stats['gerants'] ?></p>
            </div>
            <span class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center text-amber-600">
                <i class="fa-solid fa-user-tie"></i>
            </span>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 border-l-4 border-l-green-500 p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Comptes actifs</p>
                <p class="mt-3 text-3xl font-bold text-gray-900"><?= (int) $stats['actifs'] ?></p>
            </div>
            <span class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                <i class="fa-solid fa-user-check"></i>
            </span>
        </div>
    </div>
</div>

<!-- ============================== FILTRES ============================== -->
<form method="get" action="<?= $lienBase ?>"
      class="bg-white border border-gray-200 rounded-xl p-4 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-2 mb-6">
    <div class="relative lg:max-w-md w-full">
        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
        <input type="text" name="q" value="<?= e($motCle) ?>"
               placeholder="Rechercher un utilisateur..."
               class="w-full border border-gray-200 rounded-lg pl-9 pr-3 py-1.5 text-xs bg-white focus:outline-none focus:border-brand-orange">
    </div>

    <div class="flex flex-wrap items-center gap-2">
        <select name="role_id" class="border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs bg-white focus:outline-none focus:border-brand-orange">
            <option value="">Tous les rôles</option>
            <?php foreach ($roles as $role): ?>
                <option value="<?= (int) $role->id ?>" <?= (int) $roleId === (int) $role->id ? 'selected' : '' ?>>
                    <?= e($role->libelle) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="actif" class="border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs bg-white focus:outline-none focus:border-brand-orange">
            <option value="">Tous les statuts</option>
            <option value="1" <?= $actif === '1' ? 'selected' : '' ?>>Actifs</option>
            <option value="0" <?= $actif === '0' ? 'selected' : '' ?>>Désactivés</option>
        </select>

        <button type="submit"
                class="px-3 py-1.5 bg-brand-orange text-white text-xs font-medium rounded-lg hover:opacity-90 transition">
            <i class="fa-solid fa-filter mr-1"></i> Filtrer
        </button>
        <a href="<?= $lienBase ?>"
           class="px-3 py-1.5 text-xs font-medium text-gray-500 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
            Réinitialiser
        </a>
    </div>
</form>

<?php if (empty($utilisateurs)): ?>
    <div class="bg-white border border-gray-200 rounded-xl p-12 text-center text-gray-500 text-sm">
        <i class="fa-solid fa-users mb-3 text-3xl text-gray-300 block"></i>
        <?= ($motCle !== '' || $roleId !== null || $actif !== null) ? 'Aucun utilisateur ne correspond à vos critères.' : 'Aucun utilisateur pour le moment.' ?>
    </div>
<?php else: ?>

<div class="mt-4 bg-white border border-gray-200 rounded-xl overflow-hidden">
    <div class="flex items-center justify-between px-6 py-3 border-b border-gray-100">
        <span class="text-xs text-gray-400"><?= (string) $total ?> utilisateur<?= $total > 1 ? 's' : '' ?></span>
    </div>

    <div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500 whitespace-nowrap">
                <th class="px-4 py-3 font-medium">Utilisateur</th>
                <th class="px-4 py-3 font-medium">Rôle</th>
                <th class="px-4 py-3 font-medium">Statut</th>
                <th class="px-4 py-3 font-medium">Date de création</th>
                <th class="px-4 py-3 font-medium text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php foreach ($utilisateurs as $u): ?>
            <tr class="hover:bg-gray-50/50 transition">
                <td class="px-4 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-brand-orange/10 flex items-center justify-center text-brand-orange font-bold text-sm shrink-0">
                            <?= e($u->initiales()) ?>
                        </div>
                        <div class="min-w-0">
                            <p class="font-medium text-gray-900 truncate">
                                <?= e($u->nomComplet()) ?>
                                <?php if ($u->id === $utilisateurConnecteId): ?>
                                    <span class="ml-1 text-[10px] font-semibold text-brand-orange">Vous</span>
                                <?php endif; ?>
                            </p>
                            <p class="text-xs text-gray-500 truncate"><?= e($u->email) ?></p>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-4">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium whitespace-nowrap
                        <?= $u->role === 'ADMIN' ? 'bg-brand-orange/10 text-brand-orange' : 'bg-blue-50 text-blue-700' ?>">
                        <i class="fa-solid <?= $u->role === 'ADMIN' ? 'fa-user-shield' : 'fa-user-tie' ?> text-[10px]"></i>
                        <?= e($u->role) ?>
                    </span>
                </td>
                <td class="px-4 py-4">
                    <?php if ($u->actif): ?>
                        <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 whitespace-nowrap">
                            <i class="fa-solid fa-circle text-[6px]"></i> Actif
                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500 whitespace-nowrap">
                            <i class="fa-solid fa-circle text-[6px]"></i> Désactivé
                        </span>
                    <?php endif; ?>
                </td>
                <td class="px-4 py-4 text-gray-500 text-xs whitespace-nowrap"><?= e($u->dateCreationFormatee()) ?></td>
                <td class="px-4 py-4">
                    <div class="flex items-center justify-end gap-1.5">
                        <a href="<?= $lienBase ?>/<?= $u->id ?>/modifier"
                           class="w-7 h-7 flex items-center justify-center rounded-lg bg-brand-orange/10 text-brand-orange hover:bg-brand-orange hover:text-white transition"
                           title="Modifier">
                            <i class="fa-solid fa-pen text-xs"></i>
                        </a>

                        <?php if ($u->id !== $utilisateurConnecteId): ?>
                            <form method="post" action="<?= $lienBase ?>/<?= $u->id ?>/actif" class="inline">
                                <input type="hidden" name="_token" value="<?= csrf() ?>">
                                <button type="submit"
                                        class="w-7 h-7 flex items-center justify-center rounded-lg transition
                                        <?= $u->actif ? 'bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white' : 'bg-green-50 text-green-700 hover:bg-green-500 hover:text-white' ?>"
                                        title="<?= $u->actif ? 'Désactiver le compte' : 'Activer le compte' ?>">
                                    <i class="fa-solid <?= $u->actif ? 'fa-user-slash' : 'fa-user-check' ?> text-xs"></i>
                                </button>
                            </form>

                            <button type="button" onclick="openModal('suppr-modal-<?= $u->id ?>')"
                                    class="w-7 h-7 flex items-center justify-center rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition"
                                    title="Supprimer">
                                <i class="fa-solid fa-trash text-xs"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>

<?php
    $lienBasePagination = $lienBase;
    require __DIR__ . '/../partials/pagination.php';
?>

<?php endif; ?>

<!-- ============================== MODALES SUPPRESSION ============================== -->
<?php foreach ($utilisateurs as $u): ?>
    <?php if ($u->id === $utilisateurConnecteId): continue; endif; ?>
    <div id="suppr-modal-<?= $u->id ?>" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" onclick="closeModal('suppr-modal-<?= $u->id ?>')"></div>
        <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center text-red-500 text-xl shrink-0">
                    <i class="fa-solid fa-trash-can"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900">Supprimer cet utilisateur ?</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        « <?= e($u->nomComplet()) ?> » perdra définitivement l'accès à la plateforme.
                    </p>
                </div>
            </div>
            <div class="mt-6 flex items-center justify-end gap-3">
                <button type="button" onclick="closeModal('suppr-modal-<?= $u->id ?>')"
                        class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 border border-gray-200 hover:bg-gray-50 transition">
                    Annuler
                </button>
                <form method="post" action="<?= $lienBase ?>/<?= $u->id ?>/supprimer">
                    <input type="hidden" name="_token" value="<?= csrf() ?>">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white bg-red-500 hover:bg-red-600 transition">
                        <i class="fa-solid fa-trash text-xs"></i> Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<!-- ============================== DRAWER AJOUT / MODIFICATION ============================== -->
<div id="utilisateur-drawer" class="fixed inset-0 z-50 <?= $mode === '' ? 'hidden' : '' ?>">
    <div class="absolute inset-0 bg-black/50" onclick="fermerDrawer()"></div>
    <div class="absolute right-0 top-0 h-full w-full max-w-md bg-white shadow-xl flex flex-col">
        <header class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-900"><?= $mode === 'modifier' ? 'Modifier l\'utilisateur' : 'Ajouter un utilisateur' ?></h3>
            <button type="button" onclick="fermerDrawer()"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </header>

        <form method="post"
              action="<?= $mode === 'modifier' ? $lienBase . '/' . $utilisateur->id . '/modifier' : $lienBase . '/creer' ?>"
              class="flex-1 overflow-y-auto px-6 py-5 space-y-5">
            <input type="hidden" name="_token" value="<?= csrf() ?>">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="u-prenom" class="block text-sm font-medium text-gray-900 mb-1">Prénom <span class="text-red-500">*</span></label>
                    <input type="text" id="u-prenom" name="prenom" value="<?= e((string) $vPrenom) ?>"
                           placeholder="Ex : Awa"
                           class="w-full border <?= isset($errors['prenom']) ? 'border-red-400 bg-red-50' : 'border-gray-200' ?> rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-brand-orange">
                    <?php if ($errors['prenom'] ?? false): ?>
                        <p class="mt-1 text-xs text-red-600"><?= e($errors['prenom']) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="u-nom" class="block text-sm font-medium text-gray-900 mb-1">Nom <span class="text-red-500">*</span></label>
                    <input type="text" id="u-nom" name="nom" value="<?= e((string) $vNom) ?>"
                           placeholder="Ex : Diop"
                           class="w-full border <?= isset($errors['nom']) ? 'border-red-400 bg-red-50' : 'border-gray-200' ?> rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-brand-orange">
                    <?php if ($errors['nom'] ?? false): ?>
                        <p class="mt-1 text-xs text-red-600"><?= e($errors['nom']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <label for="u-email" class="block text-sm font-medium text-gray-900 mb-1">Adresse email <span class="text-red-500">*</span></label>
                <input type="email" id="u-email" name="email" value="<?= e((string) $vEmail) ?>"
                       placeholder="exemple@saveur221.sn"
                       class="w-full border <?= isset($errors['email']) ? 'border-red-400 bg-red-50' : 'border-gray-200' ?> rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-brand-orange">
                <?php if ($errors['email'] ?? false): ?>
                    <p class="mt-1 text-xs text-red-600"><?= e($errors['email']) ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label for="u-role" class="block text-sm font-medium text-gray-900 mb-1">Rôle <span class="text-red-500">*</span></label>
                <select id="u-role" name="role_id"
                        class="w-full border <?= isset($errors['role_id']) ? 'border-red-400 bg-red-50' : 'border-gray-200' ?> rounded-lg px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-brand-orange">
                    <option value="">Choisir un rôle...</option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= (int) $role->id ?>" <?= (string) $vRole === (string) $role->id ? 'selected' : '' ?>>
                            <?= e($role->libelle) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if ($errors['role_id'] ?? false): ?>
                    <p class="mt-1 text-xs text-red-600"><?= e($errors['role_id']) ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label for="u-mdp" class="block text-sm font-medium text-gray-900 mb-1">
                    Mot de passe <?= $mode === 'creer' ? '<span class="text-red-500">*</span>' : '' ?>
                </label>
                <input type="password" id="u-mdp" name="mot_de_passe" autocomplete="new-password"
                       placeholder="<?= $mode === 'modifier' ? 'Laisser vide pour conserver le mot de passe actuel' : 'Au moins 6 caractères' ?>"
                       class="w-full border <?= isset($errors['mot_de_passe']) ? 'border-red-400 bg-red-50' : 'border-gray-200' ?> rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-brand-orange">
                <?php if ($errors['mot_de_passe'] ?? false): ?>
                    <p class="mt-1 text-xs text-red-600"><?= e($errors['mot_de_passe']) ?></p>
                <?php elseif ($mode === 'modifier'): ?>
                    <p class="mt-1 text-xs text-gray-400">Laissez vide pour ne pas changer le mot de passe.</p>
                <?php endif; ?>
            </div>

            <div>
                <label for="u-mdp-conf" class="block text-sm font-medium text-gray-900 mb-1">Confirmation du mot de passe</label>
                <input type="password" id="u-mdp-conf" name="mot_de_passe_confirmation" autocomplete="new-password"
                       placeholder="Confirmez le mot de passe"
                       class="w-full border <?= isset($errors['mot_de_passe_confirmation']) ? 'border-red-400 bg-red-50' : 'border-gray-200' ?> rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-brand-orange">
                <?php if ($errors['mot_de_passe_confirmation'] ?? false): ?>
                    <p class="mt-1 text-xs text-red-600"><?= e($errors['mot_de_passe_confirmation']) ?></p>
                <?php endif; ?>
            </div>

            <div class="flex items-center justify-between bg-gray-50 border border-gray-100 rounded-lg px-4 py-3">
                <div>
                    <span class="block text-sm font-medium text-gray-900">Compte actif</span>
                    <span id="actif-label" class="text-xs <?= $actifInitiale ? 'text-green-600 font-medium' : 'text-gray-400' ?>">
                        <?= $actifInitiale ? 'Actif' : 'Désactivé' ?>
                    </span>
                </div>
                <span id="actif-switch" onclick="basculerActif()"
                      class="cursor-pointer w-12 h-6 rounded-full transition flex items-center px-0.5 <?= $actifInitiale ? 'bg-green-500 justify-end' : 'bg-gray-300 justify-start' ?>">
                    <span class="w-5 h-5 bg-white rounded-full shadow-sm"></span>
                </span>
                <input type="hidden" name="actif" id="actif-value" value="<?= $actifInitiale ? '1' : '0' ?>">
            </div>

            <div class="flex items-center gap-3 pt-2 pb-4">
                <button type="submit"
                        class="flex-1 inline-flex items-center justify-center gap-2 bg-brand-orange text-white px-4 py-2.5 rounded-lg text-sm font-semibold hover:opacity-90 transition">
                    <i class="fa-solid fa-check"></i> <?= $mode === 'modifier' ? 'Enregistrer les modifications' : 'Ajouter l\'utilisateur' ?>
                </button>
                <button type="button" onclick="fermerDrawer()"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium text-gray-600 border border-gray-200 hover:bg-gray-50 transition">
                    Annuler
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function ouvrirDrawer() {
        const el = document.getElementById('utilisateur-drawer');
        if (el) {
            el.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
    }
    function fermerDrawer() {
        const el = document.getElementById('utilisateur-drawer');
        if (el) {
            el.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }
    function basculerActif() {
        const sw = document.getElementById('actif-switch');
        const val = document.getElementById('actif-value');
        const lbl = document.getElementById('actif-label');
        if (!sw || !val || !lbl) return;
        const on = val.value === '1';
        val.value = on ? '0' : '1';
        sw.classList.toggle('bg-green-500', !on);
        sw.classList.toggle('bg-gray-300', on);
        sw.classList.toggle('justify-end', !on);
        sw.classList.toggle('justify-start', on);
        lbl.textContent = on ? 'Désactivé' : 'Actif';
        lbl.className = on ? 'text-xs text-gray-400' : 'text-xs text-green-600 font-medium';
    }
    if (document.getElementById('utilisateur-drawer') && !document.getElementById('utilisateur-drawer').classList.contains('hidden')) {
        document.body.classList.add('overflow-hidden');
    }
</script>