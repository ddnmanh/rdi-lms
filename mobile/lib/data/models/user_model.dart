class User {
  final int id;
  final String email;
  final String fullname;
  final String? birthday;
  final String? avatarPath;

  User({
    required this.id,
    required this.email,
    required this.fullname,
    this.birthday,
    this.avatarPath,
  });

  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      id: json['id'],
      email: json['email'],
      fullname: json['fullname'],
      birthday: json['birthday'],
      avatarPath: json['avatar_path'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'email': email,
      'fullname': fullname,
      'birthday': birthday,
      'avatar_path': avatarPath,
    };
  }
}
