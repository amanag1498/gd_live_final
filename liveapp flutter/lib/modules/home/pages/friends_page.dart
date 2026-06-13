import 'dart:ui';

import 'package:flutter/material.dart';
import 'package:get/get.dart';

import '../../../app/routes/app_routes.dart';
import '../../../app/brand/brand.dart';
import '../../../app/widgets/haptics.dart';

class FriendsPage extends StatelessWidget {
  final double bottomPadding;
  const FriendsPage({super.key, required this.bottomPadding});

  @override
  Widget build(BuildContext context) {
    final friends = [
      ('Aman', true, 2400),
      ('Neha', false, 18),
      ('Kartik', true, 980),
      ('Riya', false, 0),
      ('Arjun', false, 0),
      ('Asha', true, 5600),
    ];

    return ListView(
      physics: const BouncingScrollPhysics(),
      padding: EdgeInsets.fromLTRB(16, kToolbarHeight + 8, 16, bottomPadding),
      children: [
        _LiveCallsCard(
          onTap: () {
            Haptics.medium();
            Get.offAllNamed(Routes.home);
          },
        ),
        const SizedBox(height: 18),
        Text(
          'Friends',
          style: Theme.of(context).textTheme.titleLarge?.copyWith(
                color: Colors.white,
                fontWeight: FontWeight.w900,
              ),
        ),
        const SizedBox(height: 6),
        Text(
          'Your live and offline connections in one place.',
          style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                color: Colors.white.withOpacity(.66),
              ),
        ),
        const SizedBox(height: 14),
        ...friends.map((f) {
          final live = f.$2;
          return Padding(
            padding: const EdgeInsets.only(bottom: 12),
            child: _FriendRow(
              name: f.$1,
              live: live,
              watchers: f.$3,
            ),
          );
        }),
      ],
    );
  }
}

class _LiveCallsCard extends StatelessWidget {
  final VoidCallback onTap;

  const _LiveCallsCard({required this.onTap});

  @override
  Widget build(BuildContext context) {
    return ClipRRect(
      borderRadius: BorderRadius.circular(28),
      child: BackdropFilter(
        filter: ImageFilter.blur(sigmaX: 18, sigmaY: 18),
        child: Container(
          padding: const EdgeInsets.all(18),
          decoration: BoxDecoration(
            gradient: const LinearGradient(
              colors: [Color(0xFF1A1035), Color(0xFF26154C)],
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
            ),
            borderRadius: BorderRadius.circular(28),
            border: Border.all(color: Colors.white.withOpacity(.08)),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Container(
                    width: 48,
                    height: 48,
                    decoration: BoxDecoration(
                      color: Colors.white.withOpacity(.08),
                      borderRadius: BorderRadius.circular(16),
                    ),
                    child: const Icon(Icons.videocam_rounded, color: Colors.white),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'Live Rooms',
                          style: Theme.of(context).textTheme.titleLarge?.copyWith(
                                color: Colors.white,
                                fontWeight: FontWeight.w900,
                              ),
                        ),
                        const SizedBox(height: 4),
                        Text(
                          'Enter a live room or request a private video call from the host.',
                          style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                                color: Colors.white.withOpacity(.68),
                              ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 16),
              FilledButton.icon(
                onPressed: onTap,
                style: FilledButton.styleFrom(
                  backgroundColor: kGdLivePrimary,
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(horizontal: 18, vertical: 14),
                ),
                icon: const Icon(Icons.arrow_forward_rounded),
                label: const Text('Open Live Rooms'),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _FriendRow extends StatelessWidget {
  final String name;
  final bool live;
  final int watchers;

  const _FriendRow({
    required this.name,
    required this.live,
    required this.watchers,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: Colors.white.withOpacity(.08),
        borderRadius: BorderRadius.circular(22),
        border: Border.all(color: Colors.white.withOpacity(.08)),
      ),
      child: Row(
        children: [
          CircleAvatar(
            radius: 22,
            backgroundColor: kGdLivePrimary.withOpacity(.2),
            child: Text(
              name.characters.first.toUpperCase(),
              style: const TextStyle(fontWeight: FontWeight.w800, color: kGdLivePrimary),
            ),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  name,
                  style: const TextStyle(
                    fontWeight: FontWeight.w800,
                    color: Colors.white,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  live ? '$watchers watching now' : 'Offline right now',
                  style: TextStyle(
                    color: Colors.white.withOpacity(.66),
                    fontWeight: FontWeight.w600,
                  ),
                ),
              ],
            ),
          ),
          if (live)
            FilledButton(
              onPressed: () {
                Haptics.light();
                Get.offAllNamed(Routes.home);
              },
              style: FilledButton.styleFrom(backgroundColor: kGdLivePrimary),
              child: const Text('Join'),
            )
          else
            OutlinedButton(
              onPressed: () {
                Haptics.selection();
              },
              style: OutlinedButton.styleFrom(
                foregroundColor: Colors.white,
                side: BorderSide(color: Colors.white.withOpacity(.18)),
              ),
              child: const Text('Notify me'),
            ),
        ],
      ),
    );
  }
}
